CBOR Encder/Decoder for Symfony
===============================

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/spomky-labs/cbor-bundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/spomky-labs/cbor-bundle/?branch=master)
[![Coverage Status](https://coveralls.io/repos/github/spomky-labs/cbor-bundle/badge.svg?branch=master)](https://coveralls.io/github/spomky-labs/cbor-bundle?branch=master)

[![Build Status](https://travis-ci.org/spomky-labs/cbor-bundle.svg?branch=master)](https://travis-ci.org/spomky-labs/cbor-bundle)

[![Latest Stable Version](https://poser.pugx.org/spomky-labs/cbor-bundle/v/stable.png)](https://packagist.org/packages/spomky-labs/cbor-bundle)
[![Total Downloads](https://poser.pugx.org/spomky-labs/cbor-bundle/downloads.png)](https://packagist.org/packages/spomky-labs/cbor-bundle)
[![Latest Unstable Version](https://poser.pugx.org/spomky-labs/cbor-bundle/v/unstable.png)](https://packagist.org/packages/spomky-labs/cbor-bundle)
[![License](https://poser.pugx.org/spomky-labs/cbor-bundle/license.png)](https://packagist.org/packages/spomky-labs/cbor-bundle)

# Scope

This bundle wraps the [spomky-labs/cbor-php](https://github.com/Spomky-Labs/cbor-php) library and provides the decoder as a service
This will help you to eausily decode CBOR streams (Concise Binary Object Representation from [RFC7049](https://tools.ietf.org/html/rfc7049)).

# Installation

Install the bundle with Composer: `composer require spomky-labs/cbor-budle`.

This project follows the [semantic versioning](http://semver.org/) strictly.

# Documentation

## Object Creation

For object creation, please refer to [the documentation of the library](https://github.com/Spomky-Labs/cbor-php#object-creation).

## Object Loading

If you want to load a CBOR encoded data, you just have to use de decoder available from the container.

```php
<?php

use CBOR\Decoder;
use CBOR\StringStream;

// CBOR object (in hex for the example)
$data = hex2bin('fb3fd5555555555555');

// String Stream
$stream = new StringStream($data);

// Load the data
$object = $container->get(Decoder::class)->decode($stream); // Return a CBOR\OtherObject\DoublePrecisionFloatObject class with normalized value ~0.3333 (=1/3)
```

## Custom Tags / Other Objects



# Support

I bring solutions to your problems and answer your questions.

If you really love that project and the work I have done or if you want I prioritize your issues, then you can help me out for a couple of :beers: or more!

[![Become a Patreon](https://c5.patreon.com/external/logo/become_a_patron_button.png)](https://www.patreon.com/FlorentMorselli)

# Contributing

Requests for new features, bug fixed and all other ideas to make this project useful are welcome.
The best contribution you could provide is by fixing the [opened issues where help is wanted](https://github.com/spomky-labs/cbor-bundle/issues?q=is%3Aissue+is%3Aopen+label%3A%22help+wanted%22).

Please report all issues in [the main repository](https://github.com/spomky-labs/cbor-bundle/issues).

Please make sure to [follow these best practices](.github/CONTRIBUTING.md).

# Security Issues

If you discover a security vulnerability within the project, please **don't use the bug tracker and don't publish it publicly**.
Instead, all security issues must be sent to security [at] spomky-labs.com. 

# Licence

This project is release under [MIT licence](LICENSE).
