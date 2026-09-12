<?php

declare(strict_types=1);

namespace SpomkyLabs\CborBundle\Tests\Unit;

use CBOR\Decoder;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use SpomkyLabs\CborBundle\DependencyInjection\Configuration;
use SpomkyLabs\CborBundle\Tests\Fixtures\Cbor\ConfiguredSimpleObject;
use SpomkyLabs\CborBundle\Tests\Fixtures\Cbor\ConfiguredTag;
use stdClass;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;

/**
 * @internal
 */
final class ConfigurationTest extends TestCase
{
    #[Test]
    public function theDefaultsRegisterNothingBeyondTheLibrary(): void
    {
        // When
        $config = (new Processor())->processConfiguration(new Configuration('cbor'), []);

        // Then
        static::assertSame([
            'max_depth' => Decoder::DEFAULT_MAX_DEPTH,
            'tags' => [],
            'other_objects' => [],
        ], $config);
    }

    #[Test]
    public function classesAreAccepted(): void
    {
        // When
        $config = (new Processor())->processConfiguration(new Configuration('cbor'), [
            [
                'tags' => [ConfiguredTag::class],
                'other_objects' => [ConfiguredSimpleObject::class],
            ],
        ]);

        // Then
        static::assertSame([ConfiguredTag::class], $config['tags']);
        static::assertSame([ConfiguredSimpleObject::class], $config['other_objects']);
    }

    #[Test]
    public function aClassThatIsNotATagIsRejected(): void
    {
        // Then
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('The class "stdClass" does not implement CBOR\Tag\TagInterface.');

        // When
        (new Processor())->processConfiguration(new Configuration('cbor'), [[
            'tags' => [stdClass::class],
        ]]);
    }

    #[Test]
    public function aClassThatIsNotAnOtherObjectIsRejected(): void
    {
        // Then
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('The class "stdClass" does not implement CBOR\OtherObject\OtherObjectInterface.');

        // When
        (new Processor())->processConfiguration(new Configuration('cbor'), [[
            'other_objects' => [stdClass::class],
        ]]);
    }
}
