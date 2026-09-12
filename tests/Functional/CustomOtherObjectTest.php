<?php

declare(strict_types=1);

namespace SpomkyLabs\CborBundle\Tests\Functional;

use CBOR\OtherObject\OtherObjectManager;
use CBOR\OtherObject\OtherObjectManagerInterface;
use CBOR\OtherObject\SimpleObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use SpomkyLabs\CborBundle\CBORDecoder;
use SpomkyLabs\CborBundle\Tests\Fixtures\Cbor\AutoconfiguredSimpleObject;
use SpomkyLabs\CborBundle\Tests\Fixtures\Cbor\ConfiguredSimpleObject;
use SpomkyLabs\CborBundle\Tests\Fixtures\Cbor\TaggedServiceSimpleObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @internal
 */
final class CustomOtherObjectTest extends KernelTestCase
{
    #[Test]
    public function theOtherObjectManagerIsAvailableThroughItsInterface(): void
    {
        // Given
        static::bootKernel();

        // When
        $manager = static::getContainer()->get(OtherObjectManagerInterface::class);

        // Then
        static::assertInstanceOf(OtherObjectManager::class, $manager);
    }

    /**
     * @param class-string<AutoconfiguredSimpleObject|ConfiguredSimpleObject|TaggedServiceSimpleObject> $class
     */
    #[Test]
    #[DataProvider('applicationObjects')]
    public function anApplicationClassReplacesTheBuiltInOneForTheValuesItClaims(string $class, string $encoded): void
    {
        // Given
        static::bootKernel();
        /** @var CBORDecoder $decoder */
        $decoder = static::getContainer()->get(CBORDecoder::class);

        // When
        $result = $decoder->decode($encoded);

        // Then
        static::assertInstanceOf($class, $result);
    }

    #[Test]
    public function theOtherSimpleValuesAreStillDecodedByTheLibrary(): void
    {
        // Given
        static::bootKernel();
        /** @var CBORDecoder $decoder */
        $decoder = static::getContainer()->get(CBORDecoder::class);

        // When: simple value 16, which none of the fixtures claims
        $result = $decoder->decode("\xf0");

        // Then
        static::assertInstanceOf(SimpleObject::class, $result);
    }

    /**
     * @return iterable<string, array{class-string<AutoconfiguredSimpleObject|ConfiguredSimpleObject|TaggedServiceSimpleObject>, string}>
     */
    public static function applicationObjects(): iterable
    {
        // Major type 7 with the additional information the fixture claims: 0xe0 + value.
        yield 'declared in the configuration' => [ConfiguredSimpleObject::class, "\xf1"];
        yield 'declared as a tagged service' => [TaggedServiceSimpleObject::class, "\xf2"];
        yield 'discovered by autoconfiguration' => [AutoconfiguredSimpleObject::class, "\xf3"];
    }
}
