<?php

declare(strict_types=1);

namespace SpomkyLabs\CborBundle\Tests\Functional;

use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Serializer\Encoder\EncoderInterface;

/**
 * @internal
 */
final class EnumTest extends KernelTestCase
{
    #[Test]
    public function itCanEncodeBackedEnums(): void
    {
        //Given
        static::bootKernel();

        /** @var EncoderInterface $encoder */
        $encoder = static::getContainer()->get(EncoderInterface::class);

        // When - Encode a backed enum (int)
        $result = $encoder->encode(TestBackedIntEnum::CASE_ONE, 'cbor');

        //Then - Should encode the backing value (1)
        static::assertSame("\x01", $result); // CBOR for integer 1
    }

    #[Test]
    public function itCanEncodeBackedStringEnums(): void
    {
        //Given
        static::bootKernel();

        /** @var EncoderInterface $encoder */
        $encoder = static::getContainer()->get(EncoderInterface::class);

        // When - Encode a backed enum (string)
        $result = $encoder->encode(TestBackedStringEnum::FOO, 'cbor');

        //Then - Should encode the backing value ("foo")
        static::assertSame(hex2bin('63666f6f'), $result); // CBOR for "foo"
    }

    #[Test]
    public function itCanEncodeUnitEnums(): void
    {
        //Given
        static::bootKernel();

        /** @var EncoderInterface $encoder */
        $encoder = static::getContainer()->get(EncoderInterface::class);

        // When - Encode a unit enum
        $result = $encoder->encode(TestUnitEnum::BAR, 'cbor');

        //Then - Should encode the name ("BAR")
        static::assertSame(hex2bin('63424152'), $result); // CBOR for "BAR"
    }
}

enum TestBackedIntEnum: int
{
    case CASE_ONE = 1;
    case CASE_TWO = 2;
}

enum TestBackedStringEnum: string
{
    case FOO = 'foo';
    case BAR = 'bar';
}

enum TestUnitEnum
{
    case FOO;
    case BAR;
}
