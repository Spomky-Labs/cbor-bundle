<?php

declare(strict_types=1);

namespace SpomkyLabs\CborBundle\Tests\Functional;

use CBOR\Tag\CoseSign1Tag;
use CBOR\Tag\DateTag;
use CBOR\Tag\SetTag;
use CBOR\Tag\TypedArray\Uint8ArrayTag;
use CBOR\Tag\UuidTag;
use function hex2bin;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use SpomkyLabs\CborBundle\CBORDecoder;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * The decoder service is built with the bundle's own managers, not the ones the library falls back to; this checks
 * that they know the registry tags the library implements since 3.4.
 *
 * @internal
 */
final class RegistryTagsTest extends KernelTestCase
{
    /**
     * @param class-string $class
     */
    #[Test]
    #[DataProvider('registryTags')]
    public function aRegistryTagIsDecodedIntoTheClassOfTheLibrary(string $hex, string $class): void
    {
        // Given
        static::bootKernel();
        /** @var CBORDecoder $decoder */
        $decoder = static::getContainer()->get(CBORDecoder::class);

        // When
        $result = $decoder->decode((string) hex2bin($hex));

        // Then
        static::assertInstanceOf($class, $result);
    }

    /**
     * @return iterable<string, array{string, class-string}>
     */
    public static function registryTags(): iterable
    {
        yield 'tag 18: COSE_Sign1' => ['d28440a04301020343aabbcc', CoseSign1Tag::class];
        yield 'tag 37: UUID' => ['d82550123e4567e89b12d3a456426614174000', UuidTag::class];
        yield 'tag 64: uint8 typed array' => ['d84043010203', Uint8ArrayTag::class];
        yield 'tag 100: days since the epoch' => ['d864194d19', DateTag::class];
        yield 'tag 258: set' => ['d90102820102', SetTag::class];
    }
}
