# UPGRADE FROM 3.x to 4.0

## Table of Contents

1. [Summary](#summary)
2. [Breaking Changes](#breaking-changes)
3. [New Features](#new-features)
4. [Migration Guide](#migration-guide)

## Summary

Version 4.0 is a **major feature release** that adds full Symfony Serializer integration through the new `CBOREncoder` class.

**Good news:** If you only used `CBORDecoder` in version 3.x, **there are NO breaking changes**. Your code will continue to work without modifications.

The new features are entirely additive:
- ✅ `CBORDecoder` - Still works exactly the same
- ✨ `CBOREncoder` - NEW class for Symfony Serializer integration
- ✨ Full type support (objects, enums, arrays, etc.)
- ✨ Context options for encoding control

## Breaking Changes

**There are NO breaking changes** if you were using the bundle in version 3.x with `CBORDecoder`.

### What Changed

**Version 3.x provided:**
- `CBORDecoder` - Low-level CBOR decoding only

**Version 4.0 adds:**
- `CBOREncoder` - NEW Symfony Serializer encoder/decoder
- Service aliases: `cbor.encoder` and `cbor.decoder`
- Full object serialization support
- Enum support
- Context options

### PHP Version Requirement

- **3.x:** PHP 8.2+
- **4.0:** PHP 8.3+

If you're still on PHP 8.2, you'll need to upgrade to PHP 8.3 or higher.

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

### 2. Object Serialization

The encoder works seamlessly with Symfony's Serializer to serialize/deserialize PHP objects:

```php
use App\Model\Person;
use Symfony\Component\Serializer\SerializerInterface;

$serializer = $container->get(SerializerInterface::class);

// Create and populate an object
$person = new Person();
$person->setName('foo');
$person->setAge(99);
$person->setSportsperson(false);

// Serialize to CBOR
$cborContent = $serializer->serialize($person, 'cbor');
// $cborContent contains: A4646E616D6563666F6F6361676518636C73706F727473706572736F6EF469637265617465644174F6

// Deserialize back to object
$person = $serializer->deserialize($cborContent, Person::class, 'cbor');

// Object is fully reconstructed
assert($person->getName() === 'foo');
assert($person->getAge() === 99);
assert($person->isSportsperson() === false);
```

**Key benefits:**
- Works with any serializable PHP object
- Supports nested objects and complex structures
- Handles DateTime objects automatically
- Compatible with all Symfony normalizers

**Example with arrays of objects:**
```php
$people = [
    (new Person())->setName('Alice')->setAge(30),
    (new Person())->setName('Bob')->setAge(25),
];

$cbor = $serializer->serialize($people, 'cbor');
$deserializedPeople = $serializer->deserialize($cbor, Person::class . '[]', 'cbor');
```

### 3. Enum Support

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

### 4. Context Options for Encoding

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

### 5. Improved Error Handling

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

### 6. Comprehensive Documentation

All classes now have detailed PHPDoc comments:

- Complete method documentation
- Parameter and return type descriptions
- Usage examples
- Links to CBOR specification (RFC 8949)

## Migration Guide

### Step 1: Update PHP Version (Required)

Ensure you're running PHP 8.3 or higher:

```bash
php -v
```

If you're on PHP 8.2, upgrade to PHP 8.3+ before updating to version 4.0.

### Step 2: Update the Bundle

Update the bundle via Composer:

```bash
composer require spomky-labs/cbor-bundle:^4.0
```

### Step 3: Verify Existing Code (CBORDecoder users)

**If you only used `CBORDecoder` in version 3.x, you're done!** No changes needed.

Your existing code will continue to work:

```php
use SpomkyLabs\CborBundle\CBORDecoder;

class MyService
{
    public function __construct(
        private CBORDecoder $decoder
    ) {}

    public function decode(string $cborData): CBORObject
    {
        return $this->decoder->decode($cborData);
    }
}
```

### Step 4: (Optional) Start Using New Features

If you want to use the new Symfony Serializer integration:

#### Option A: Use the Serializer (Recommended)

```php
use Symfony\Component\Serializer\SerializerInterface;

class MyService
{
    public function __construct(
        private SerializerInterface $serializer
    ) {}

    public function encodeData(array $data): string
    {
        return $this->serializer->serialize($data, 'cbor');
    }

    public function decodeData(string $cborData): array
    {
        return $this->serializer->deserialize($cborData, 'array', 'cbor');
    }
}
```

#### Option B: Use CBOREncoder Directly

```php
use SpomkyLabs\CborBundle\CBOREncoder;

class MyService
{
    public function __construct(
        private CBOREncoder $encoder
    ) {}
}
```

#### Option C: Use Service Aliases

```php
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class MyService
{
    public function __construct(
        #[Autowire(service: 'cbor.encoder')]
        private $encoder
    ) {}
}
```

### Step 5: Test Your Application

Run your test suite to ensure everything works:

```bash
php bin/phpunit
```

### Step 6: (Optional) Explore New Features

Take advantage of the new capabilities:
- Serialize PHP objects to CBOR
- Use context options for encoding control
- Handle enums natively
- Better error messages for debugging

## Questions?

If you encounter any issues during migration, please:
1. Check the [GitHub Issues](https://github.com/spomky-labs/cbor-bundle/issues)
2. Review the [CBOR specification](https://www.rfc-editor.org/rfc/rfc8949.html)
3. Open a new issue with details about your problem
