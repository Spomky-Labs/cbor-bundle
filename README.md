# CBOR Bundle for Symfony

[![Build Status](https://github.com/spomky-labs/cbor-bundle/actions/workflows/ci.yml/badge.svg)](https://github.com/spomky-labs/cbor-bundle/actions/workflows/ci.yml)
[![Latest Stable Version](https://poser.pugx.org/spomky-labs/cbor-bundle/v/stable.svg)](https://packagist.org/packages/spomky-labs/cbor-bundle)
[![Total Downloads](https://poser.pugx.org/spomky-labs/cbor-bundle/downloads.svg)](https://packagist.org/packages/spomky-labs/cbor-bundle)
[![License](https://poser.pugx.org/spomky-labs/cbor-bundle/license.svg)](https://packagist.org/packages/spomky-labs/cbor-bundle)

[![OpenSSF Scorecard](https://api.securityscorecards.dev/projects/github.com/spomky-labs/cbor-bundle/badge)](https://securityscorecards.dev/viewer/?uri=github.com/spomky-labs/cbor-bundle)

A Symfony bundle that provides CBOR (Concise Binary Object Representation) encoding and decoding support for the Symfony Serializer component.

CBOR is a data format whose design goals include the possibility of extremely small code size, fairly small message size, and extensibility without the need for version negotiation. It is defined in [RFC 8949](https://www.rfc-editor.org/rfc/rfc8949.html).

## Features

- 🔄 **Full Symfony Serializer Integration**: Works seamlessly with Symfony's serializer component
- 📦 **Complete Type Support**: Encode/decode all PHP types (scalars, arrays, objects, enums)
- ⚙️ **Context Options**: Fine-grained control over encoding behavior
- 🎯 **Object Serialization**: Serialize any PHP object to CBOR format
- 🔢 **Enum Support**: Native support for PHP 8.1+ backed and unit enums
- 📝 **Well Documented**: Comprehensive PHPDoc and guides
- ✅ **Well Tested**: Extensive test coverage

## Requirements

- PHP 8.3 or higher
- Symfony 6.4, 7.x, or 8.x

## Installation

Install the bundle using Composer:

```bash
composer require spomky-labs/cbor-bundle
```

If you're using Symfony Flex, the bundle will be automatically registered. Otherwise, register it manually in `config/bundles.php`:

```php
return [
    // ...
    SpomkyLabs\CborBundle\SpomkyLabsCborBundle::class => ['all' => true],
];
```

## Configuration

The bundle works out of the box. A single option controls how deeply nested a CBOR data item may be before the
decoder rejects it:

```yaml
# config/packages/cbor.yaml
cbor:
    max_depth: 1000 # Default value
```

Decoding is a recursive operation: a deeply nested payload — nested arrays, maps or tags cost one byte per level —
can exhaust the call stack and crash the PHP process. Data nested deeper than `max_depth` is rejected with an
`InvalidArgumentException` instead. When the data comes from an untrusted source, a much lower value is recommended:

```yaml
cbor:
    max_depth: 32
```

## Basic Usage

### Encoding Data

```php
use Symfony\Component\Serializer\SerializerInterface;

class MyController
{
    public function __construct(
        private SerializerInterface $serializer
    ) {}

    public function encodeAction(): Response
    {
        $data = [
            'name' => 'John Doe',
            'age' => 30,
            'active' => true,
            'tags' => ['developer', 'symfony']
        ];

        // Serialize to CBOR format
        $cborData = $this->serializer->serialize($data, 'cbor');

        return new Response($cborData, 200, [
            'Content-Type' => 'application/cbor'
        ]);
    }
}
```

### Decoding Data

```php
public function decodeAction(Request $request): Response
{
    $cborData = $request->getContent();

    // Deserialize from CBOR format
    $data = $this->serializer->deserialize($cborData, 'array', 'cbor');

    return $this->json($data);
}
```

### Object Serialization

```php
use App\Entity\Person;

class PersonController
{
    public function serializeObject(Person $person): string
    {
        // Serialize object to CBOR
        return $this->serializer->serialize($person, 'cbor');
    }

    public function deserializeObject(string $cborData): Person
    {
        // Deserialize CBOR back to object
        return $this->serializer->deserialize($cborData, Person::class, 'cbor');
    }
}
```

## Advanced Usage

### Context Options

You can control encoding behavior using context options:

```php
// Use single precision floats (smaller size, less precision)
$cbor = $serializer->serialize($data, 'cbor', [
    'cbor_single_precision_float' => true
]);

// Use indefinite length encoding for arrays
$cbor = $serializer->serialize([1, 2, 3], 'cbor', [
    'cbor_indefinite_list' => true
]);

// Use indefinite length encoding for maps
$cbor = $serializer->serialize(['a' => 1], 'cbor', [
    'cbor_indefinite_map' => true
]);

// Use indefinite length for text strings
$cbor = $serializer->serialize('Hello', 'cbor', [
    'cbor_indefinite_text_string' => true
]);
```

**Available context options:**
- `cbor_single_precision_float` (bool): Use 32-bit floats instead of 64-bit
- `cbor_indefinite_text_string` (bool): Use indefinite length for text strings
- `cbor_indefinite_byte_string` (bool): Use indefinite length for byte strings
- `cbor_indefinite_list` (bool): Use indefinite length for arrays
- `cbor_indefinite_map` (bool): Use indefinite length for maps

### Enum Support

The bundle natively supports PHP 8.1+ enums:

```php
// Backed enums are encoded as their backing value
enum Status: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
}

$cbor = $serializer->serialize(Status::ACTIVE, 'cbor');
// Encodes as "active"

// Unit enums are encoded as their name
enum Color
{
    case RED;
    case GREEN;
}

$cbor = $serializer->serialize(Color::RED, 'cbor');
// Encodes as "RED"
```

### Direct Service Access

You can also use the encoder/decoder services directly:

```php
use SpomkyLabs\CborBundle\CBOREncoder;
use SpomkyLabs\CborBundle\CBORDecoder;

class MyService
{
    public function __construct(
        private CBOREncoder $encoder,
        private CBORDecoder $decoder
    ) {}
}
```

Or use the service aliases:

```php
class MyService
{
    public function __construct(
        #[Autowire(service: 'cbor.encoder')]
        private $encoder,
        #[Autowire(service: 'cbor.decoder')]
        private $decoder
    ) {}
}
```

## Supported Types

The bundle supports encoding and decoding of:

- **Scalars**: `int`, `float`, `string`, `bool`, `null`
- **Arrays**: Indexed arrays (as CBOR lists) and associative arrays (as CBOR maps)
- **Objects**: Any object that can be normalized by Symfony's normalizers
- **Enums**: PHP 8.1+ backed and unit enums
- **DateTime**: Automatically handled via Symfony's normalizers
- **Nested structures**: Arrays of objects, objects containing arrays, etc.

## Extending the Bundle

The decoder service is built with a tag manager and an "other object" manager that know every tag class
[spomky-labs/cbor-php](https://github.com/Spomky-Labs/cbor-php) implements from the IANA registry -- date/time,
big numbers, UUIDs, COSE messages and CBOR Web Tokens, typed arrays, IP addresses and so on -- and every simple
value and float of major type 7. Both managers are services you can inject through their interface:

```php
use CBOR\OtherObject\OtherObjectManagerInterface;
use CBOR\Tag\TagManagerInterface;

class MyService
{
    public function __construct(
        private TagManagerInterface $tagManager,
        private OtherObjectManagerInterface $otherObjectManager
    ) {}
}
```

Tag classes and other object classes are not services: the decoder instantiates them itself, with the data of each
item it reads. Registering one is therefore a matter of naming the class, and a class you register for a tag number
(or a simple value) the library already handles replaces the built-in one.

### Custom CBOR Tags

Implement `CBOR\Tag\TagInterface` (usually by extending `CBOR\Tag`, see the
[library guide](https://github.com/Spomky-Labs/cbor-php/blob/3.4.x/doc/custom-tags.md)) and list the class in the
configuration:

```yaml
# config/packages/cbor.yaml
cbor:
    tags:
        - App\Cbor\CustomTag
```

A class that lives in a directory the service container discovers (`src/` in a standard application) is registered
automatically: the bundle autoconfigures every `TagInterface` implementation with the `cbor.tag` service tag, and
only takes the class of such a service definition -- the container never tries to build it. The same tag can be set
by hand on a class that is not discovered:

```yaml
services:
    App\Cbor\CustomTag:
        tags: ['cbor.tag']
```

### Custom CBOR Objects

Major type 7 items -- simple values, floats, the break code -- work the same way with `CBOR\OtherObjectInterface`
(usually by extending `CBOR\OtherObject`), the `other_objects` configuration key and the `cbor.other_object`
service tag:

```yaml
cbor:
    other_objects:
        - App\Cbor\CustomObject
```

## Upgrading

See [UPGRADE-4.0.md](UPGRADE-4.0.md) for migration instructions from version 3.x to 4.0.

## Documentation

For more detailed information about CBOR:
- [RFC 8949 - CBOR Specification](https://www.rfc-editor.org/rfc/rfc8949.html)
- [CBOR Website](https://cbor.io/)

## Contributing

Contributions are welcome! Please read our [contributing guidelines](/.github/CONTRIBUTING.md) before submitting a pull request.

## Security

If you discover a security vulnerability, please follow our [security policy](SECURITY.md).

## Support

If you find this project useful and want to support its development:

- ⭐ Star the project on GitHub
- 💰 [Become a sponsor](https://github.com/sponsors/Spomky)
- ☕ [Support via Patreon](https://www.patreon.com/FlorentMorselli)

## License

This project is released under the [MIT License](LICENSE).

## Credits

This bundle is maintained by [Florent Morselli](https://github.com/Spomky) and [contributors](https://github.com/spomky-labs/cbor-bundle/contributors).
