<?php

declare(strict_types=1);

namespace SpomkyLabs\CborBundle\Tests\Functional;

use CBOR\Tag\GenericTag;
use CBOR\Tag\TagManager;
use CBOR\Tag\TagManagerInterface;
use CBOR\UnsignedIntegerObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use SpomkyLabs\CborBundle\CBORDecoder;
use SpomkyLabs\CborBundle\Tests\Fixtures\Cbor\AutoconfiguredTag;
use SpomkyLabs\CborBundle\Tests\Fixtures\Cbor\ConfiguredTag;
use SpomkyLabs\CborBundle\Tests\Fixtures\Cbor\TaggedServiceTag;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @internal
 */
final class CustomTagTest extends KernelTestCase
{
    #[Test]
    public function theTagManagerIsAvailableThroughItsInterface(): void
    {
        // Given
        static::bootKernel();

        // When
        $manager = static::getContainer()->get(TagManagerInterface::class);

        // Then
        static::assertInstanceOf(TagManager::class, $manager);
    }

    /**
     * @param class-string<AutoconfiguredTag|ConfiguredTag|TaggedServiceTag> $class
     */
    #[Test]
    #[DataProvider('applicationTags')]
    public function anApplicationTagIsDecodedIntoItsClass(string $class, string $encoded): void
    {
        // Given
        static::bootKernel();
        /** @var CBORDecoder $decoder */
        $decoder = static::getContainer()->get(CBORDecoder::class);

        // When
        $result = $decoder->decode($encoded);

        // Then
        static::assertInstanceOf($class, $result);
        static::assertInstanceOf(UnsignedIntegerObject::class, $result->getValue());
        static::assertSame('42', $result->getValue()->normalize());
    }

    #[Test]
    public function aTagNobodyRegisteredIsStillDecodedAsAGenericTag(): void
    {
        // Given
        static::bootKernel();
        /** @var CBORDecoder $decoder */
        $decoder = static::getContainer()->get(CBORDecoder::class);

        // When: tag 4003, one past the fixtures
        $result = $decoder->decode("\xd9\x0f\xa3\x18\x2a");

        // Then
        static::assertInstanceOf(GenericTag::class, $result);
    }

    /**
     * @return iterable<string, array{class-string<AutoconfiguredTag|ConfiguredTag|TaggedServiceTag>, string}>
     */
    public static function applicationTags(): iterable
    {
        // Each fixture wraps the unsigned integer 42 (0x18 0x2a) in a two byte tag number (0xd9 + big endian id).
        yield 'declared in the configuration' => [ConfiguredTag::class, "\xd9\x0f\xa0\x18\x2a"];
        yield 'declared as a tagged service' => [TaggedServiceTag::class, "\xd9\x0f\xa1\x18\x2a"];
        yield 'discovered by autoconfiguration' => [AutoconfiguredTag::class, "\xd9\x0f\xa2\x18\x2a"];
    }
}
