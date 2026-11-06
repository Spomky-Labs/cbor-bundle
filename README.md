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

## Documentation

The documentation is available at https://cbor.spomky-labs.com/latest/bundle/installation/

Those pages describe version 3.2 of the bundle, the one paired with the version of the library the site
currently publishes. On this branch, tag and simple value classes are registered through the `cbor.tag`
and `cbor.other_object` service tags; the `tags` and `other_objects` configuration keys the pages mention
are not available here.

## Upgrading

See [UPGRADE-4.0.md](UPGRADE-4.0.md) for migration instructions from version 3.x to 4.0.

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
