# UPGRADE FROM 3.x to 4.0

## Table of Contents

1. [Breaking Changes](#breaking-changes)
2. [New Features](#new-features)
3. [Migration Guide](#migration-guide)

## Breaking Changes

### Class Renaming

**Before (3.x):**
```php
use SpomkyLabs\CborBundle\Normalizer\CBORNormalizer;

class MyService
{
    public function __construct(
        private CBORNormalizer $normalizer
    ) {}
}
```

**After (4.0):**
```php
use SpomkyLabs\CborBundle\CBOREncoder;

class MyService
{
    public function __construct(
        private CBOREncoder $encoder
    ) {}
}
```

### Namespace Changes

- **Old:** `SpomkyLabs\CborBundle\Normalizer\CBORNormalizer`
- **New:** `SpomkyLabs\CborBundle\CBOREncoder`

The class was renamed to better reflect its purpose. It implements `EncoderInterface` and `DecoderInterface`, not `NormalizerInterface`.

### File Location Changes

- **Old:** `src/Normalizer/CBORNormalizer.php`
- **New:** `src/CBOREncoder.php`

The encoder is now in the root `src/` directory alongside `CBORDecoder.php`.

## New Features

### 1. Full Type Support

The encoder now supports all PHP types:

```php
use Symfony\Component\Serializer\SerializerInterface;

$serializer = $container->get(SerializerInterface::class);

// Booleans
$cbor = $serializer->serialize(true, 'cbor');
$cbor = $serializer->serialize(false, 'cbor');

// Null
$cbor = $serializer->serialize(null, 'cbor');

// Floats
$cbor = $serializer->serialize(3.14, 'cbor');

// Arrays (indexed)
$cbor = $serializer->serialize([1, 2, 3], 'cbor');

// Arrays (associative)
$cbor = $serializer->serialize(['key' => 'value'], 'cbor');

// Nested structures
$cbor = $serializer->serialize([
    'user' => [
        'id' => 123,
        'active' => true,
        'tags' => ['admin', 'developer']
    ]
], 'cbor');
```

### 2. Enum Support

PHP 8.1+ enums are now fully supported:

```php
// Backed enums - encoded as their backing value
enum Status: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
}

$cbor = $serializer->serialize(Status::ACTIVE, 'cbor');
// Encodes as "active"

// Unit enums - encoded as their name
enum Color
{
    case RED;
    case GREEN;
}

$cbor = $serializer->serialize(Color::RED, 'cbor');
// Encodes as "RED"
```

### 3. Context Options for Encoding

You can now control encoding behavior using context options:

```php
use Symfony\Component\Serializer\SerializerInterface;

$serializer = $container->get(SerializerInterface::class);

// Use single precision floats (smaller size, less precision)
$cbor = $serializer->serialize(1.1, 'cbor', [
    'cbor_single_precision_float' => true
]);

// Use indefinite length for text strings
$cbor = $serializer->serialize('Hello World', 'cbor', [
    'cbor_indefinite_text_string' => true
]);

// Use indefinite length for arrays
$cbor = $serializer->serialize([1, 2, 3], 'cbor', [
    'cbor_indefinite_list' => true
]);

// Use indefinite length for maps
$cbor = $serializer->serialize(['a' => 1], 'cbor', [
    'cbor_indefinite_map' => true
]);

// Use indefinite length for byte strings
$binaryData = random_bytes(100);
$cbor = $serializer->serialize($binaryData, 'cbor', [
    'cbor_indefinite_byte_string' => true
]);
```

**Available context options:**
- `cbor_single_precision_float` (bool): Use 32-bit floats instead of 64-bit
- `cbor_indefinite_text_string` (bool): Use indefinite length encoding for text strings
- `cbor_indefinite_byte_string` (bool): Use indefinite length encoding for byte strings
- `cbor_indefinite_list` (bool): Use indefinite length encoding for arrays
- `cbor_indefinite_map` (bool): Use indefinite length encoding for maps

### 4. Improved Error Handling

Encoding and decoding errors now provide clearer error messages:

```php
try {
    $result = $serializer->deserialize($invalidCborData, 'array', 'cbor');
} catch (\InvalidArgumentException $e) {
    // Error message: "Unable to decode CBOR data: <specific error>"
}

try {
    $cbor = $serializer->serialize($unsupportedType, 'cbor');
} catch (\InvalidArgumentException $e) {
    // Error message: "Unable to encode data to CBOR format: <specific error>"
}
```

### 5. Comprehensive Documentation

All classes now have detailed PHPDoc comments:

- Complete method documentation
- Parameter and return type descriptions
- Usage examples
- Links to CBOR specification (RFC 8949)

## Migration Guide

### Step 1: Update Service Injections

If you're injecting the encoder directly:

```diff
use SpomkyLabs\CborBundle\Normalizer\CBORNormalizer;
+use SpomkyLabs\CborBundle\CBOREncoder;

class MyService
{
    public function __construct(
-       private CBORNormalizer $normalizer
+       private CBOREncoder $encoder
    ) {}
}
```

### Step 2: Update Service Configuration (if using manual wiring)

If you have custom service definitions:

```diff
# config/services.yaml
services:
-   app.cbor_service:
-       class: SpomkyLabs\CborBundle\Normalizer\CBORNormalizer
+   app.cbor_service:
+       class: SpomkyLabs\CborBundle\CBOREncoder
        # ... rest of configuration
```

### Step 3: Verify Autowiring

If you're using Symfony's autowiring (recommended), no changes are needed. The encoder will be automatically injected as `EncoderInterface` or `DecoderInterface`.

### Step 4: Test Your Application

Run your test suite to ensure everything works as expected:

```bash
php bin/phpunit
```

### Step 5: (Optional) Use New Features

Consider using the new features:
- Add context options for better control over encoding
- Use enum support if you're on PHP 8.1+
- Take advantage of improved error messages for debugging

## Questions?

If you encounter any issues during migration, please:
1. Check the [GitHub Issues](https://github.com/spomky-labs/cbor-bundle/issues)
2. Review the [CBOR specification](https://www.rfc-editor.org/rfc/rfc8949.html)
3. Open a new issue with details about your problem
