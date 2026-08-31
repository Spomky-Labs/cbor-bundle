<?php

declare(strict_types=1);

namespace SpomkyLabs\CborBundle\Tests\Functional;

use function is_string;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use function strlen;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @internal
 */
final class SerializerTest extends KernelTestCase
{
    #[Test]
    public static function theSerializerIsAvailable(): void
    {
        // Given
        static::bootKernel();

        // Then
        static::assertTrue(static::getContainer()->has(NormalizerInterface::class));
    }

    #[Test]
    #[DataProvider('getInputs')]
    #[Depends('theSerializerIsAvailable')]
    public static function theSerializerCanDecodeInputs(string $data, mixed $expectedNormalizedValue): void
    {
        // Given
        static::bootKernel();

        /** @var DecoderInterface $decoder */
        $decoder = static::getContainer()->get(DecoderInterface::class);
        $binary = hex2bin($data);
        if (! is_string($binary)) {
            throw new RuntimeException('Invalid test case');
        }

        // When
        $result = $decoder->decode($binary, 'cbor');

        // Then
        static::assertSame($expectedNormalizedValue, $result);
    }

    #[Test]
    #[DataProvider('getEncodingInputs')]
    #[Depends('theSerializerIsAvailable')]
    public static function theSerializerCanEncodeInputs(string $data, mixed $expectedNormalizedValue): void
    {
        // Given
        static::bootKernel();

        /** @var EncoderInterface $encoder */
        $encoder = static::getContainer()->get(EncoderInterface::class);
        $expectedBinary = hex2bin($data);
        if (! is_string($expectedBinary)) {
            throw new RuntimeException('Invalid test case');
        }

        // When
        $result = $encoder->encode($expectedNormalizedValue, 'cbor');

        // Then
        static::assertSame($expectedBinary, $result);
    }

    public static function getInputs(): iterable
    {
        yield ['00', 0];
        yield ['01', 1];
        yield ['0a', 10];
        yield ['17', 23];
        yield ['1818', 24];
        yield ['1819', 25];
        yield ['1864', 100];
        yield ['1903e8', 1000];
        yield ['1a000f4240', 1000000];
        yield ['1b000000e8d4a51000', 1000000000000];
        yield ['20', -1];
        yield ['29', -10];
        yield ['3863', -100];
        yield ['3903e7', -1000];
        yield ['c349010000000000000000', '-18446744073709551617'];
        yield ['3bffffffffffffffff', '-18446744073709551616'];
        yield ['1bffffffffffffffff', '18446744073709551615'];
        yield ['c249010000000000000000', '18446744073709551616'];
        yield ['7f624865626c6c616fff', 'Hello'];
        yield ['7f612863efbda163e2979563e280bf63e2979563efbda16129ff', '(｡◕‿◕｡)'];
        yield ['6548656c6c6f', 'Hello'];
        yield ['7128efbda1e29795e280bfe29795efbda129', '(｡◕‿◕｡)'];
        yield ['781948656c6c6f48656c6c6f48656c6c6f48656c6c6f48656c6c6f', 'HelloHelloHelloHelloHello'];
        yield ['5f424865426c6c416fff', 'Hello'];
        yield ['5f412843efbda143e2979543e280bf43e2979543efbda14129ff', '(｡◕‿◕｡)'];
        yield ['4548656c6c6f', 'Hello'];
        yield ['5128efbda1e29795e280bfe29795efbda129', '(｡◕‿◕｡)'];
        yield ['581948656c6c6f48656c6c6f48656c6c6f48656c6c6f48656c6c6f', 'HelloHelloHelloHelloHello'];
    }

    public static function getEncodingInputs(): iterable
    {
        // Integers (positive) - encoding takes PHP int as input
        yield ['00', 0];
        yield ['01', 1];
        yield ['0a', 10];
        yield ['17', 23];
        yield ['1818', 24];
        yield ['1819', 25];
        yield ['1864', 100];
        yield ['1903e8', 1000];
        yield ['1a000f4240', 1000000];

        // Integers (negative) - encoding takes PHP int as input
        yield ['20', -1];
        yield ['29', -10];
        yield ['3863', -100];
        yield ['3903e7', -1000];

        // Text strings (definite length)
        yield ['6548656c6c6f', 'Hello'];
        yield ['7128efbda1e29795e280bfe29795efbda129', '(｡◕‿◕｡)'];
        yield ['781948656c6c6f48656c6c6f48656c6c6f48656c6c6f48656c6c6f', 'HelloHelloHelloHelloHello'];

        // Booleans
        yield ['f4', false];
        yield ['f5', true];

        // Null
        yield ['f6', null];

        // Floats
        yield ['fb3ff199999999999a', 1.1];
        yield ['fb400921fb54442d18', 3.141592653589793];
        yield ['fbbff199999999999a', -1.1];

        // Arrays (lists)
        yield ['80', []];
        yield ['83010203', [1, 2, 3]];
        yield ['8301820203820405', [1, [2, 3], [4, 5]]];

        // Maps (associative arrays - note: in PHP, empty array is always a list)
        yield [
            'a201020304', [
                1 => 2,
                3 => 4,
            ]];
        yield [
            'a26161016162820203', [
                'a' => 1,
                'b' => [2, 3],
            ]];
    }

    #[Test]
    #[Depends('theSerializerIsAvailable')]
    public static function theSerializerCanEncodeWithContextOptions(): void
    {
        // Given
        static::bootKernel();

        /** @var EncoderInterface $encoder */
        $encoder = static::getContainer()->get(EncoderInterface::class);

        // When - Encode with single precision float
        $resultSinglePrecision = $encoder->encode(1.1, 'cbor', [
            'cbor_single_precision_float' => true,
        ]);
        $resultDoublePrecision = $encoder->encode(1.1, 'cbor', [
            'cbor_single_precision_float' => false,
        ]);

        // Then - Single precision should be shorter
        static::assertLessThan(strlen($resultDoublePrecision), strlen($resultSinglePrecision));
        static::assertSame("\xfa\x3f\x8c\xcc\xcd", $resultSinglePrecision); // single precision
        static::assertSame(hex2bin('fb3ff199999999999a'), $resultDoublePrecision); // double precision
    }
}
