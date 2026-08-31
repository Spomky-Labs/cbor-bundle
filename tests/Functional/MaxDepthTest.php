<?php

declare(strict_types=1);

namespace SpomkyLabs\CborBundle\Tests\Functional;

use CBOR\Decoder;
use CBOR\ListObject;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use SpomkyLabs\CborBundle\CBORDecoder;
use function str_repeat;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @internal
 */
final class MaxDepthTest extends KernelTestCase
{
    private const CONFIGURED_MAX_DEPTH = 64;

    #[Test]
    public function theConfiguredMaximumDepthIsAvailableAsAParameter(): void
    {
        // Given
        static::bootKernel();

        // Then
        static::assertSame(self::CONFIGURED_MAX_DEPTH, static::getContainer()->getParameter('cbor.max_depth'));
        static::assertNotSame(Decoder::DEFAULT_MAX_DEPTH, self::CONFIGURED_MAX_DEPTH);
    }

    #[Test]
    public function dataNestedWithinTheConfiguredDepthIsDecoded(): void
    {
        // Given
        static::bootKernel();
        /** @var CBORDecoder $decoder */
        $decoder = static::getContainer()->get(CBORDecoder::class);

        // When
        $result = $decoder->decode(str_repeat("\x81", self::CONFIGURED_MAX_DEPTH) . "\x00");

        // Then
        static::assertInstanceOf(ListObject::class, $result);
        static::assertCount(1, $result);
    }

    #[Test]
    public function dataNestedDeeperThanTheConfiguredDepthIsRejected(): void
    {
        // Given
        static::bootKernel();
        /** @var CBORDecoder $decoder */
        $decoder = static::getContainer()->get(CBORDecoder::class);

        // Then
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot parse the data. Maximum nesting depth of 64 exceeded.');

        // When
        $decoder->decode(str_repeat("\x81", self::CONFIGURED_MAX_DEPTH + 1) . "\x00");
    }
}
