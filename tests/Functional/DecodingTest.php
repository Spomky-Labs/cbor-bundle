<?php

declare(strict_types=1);

namespace SpomkyLabs\CborBundle\Tests\Functional;

use CBOR\Decoder;
use CBOR\Normalizable;
use function is_string;
use RuntimeException;
use SpomkyLabs\CborBundle\CBORDecoder;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @internal
 */
final class DecodingTest extends KernelTestCase
{
    /**
     * @test
     */
    public static function theDecoderServiceIsAvailable(): void
    {
        //Given
        static::bootKernel();

        //Then
        static::assertTrue(static::getContainer()->has(Decoder::class));
    }

    /**
     * @test
     * @depends theDecoderServiceIsAvailable
     *
     * @dataProvider getInputs
     */
    public static function theDecoderCanDecodeInputs(string $data, string $expectedNormalizedValue): void
    {
        //Given
        static::bootKernel();

        /** @var CBORDecoder $decoder */
        $decoder = static::getContainer()->get(CBORDecoder::class);
        $binary = hex2bin($data);
        if (! is_string($binary)) {
            throw new RuntimeException('Invalid test case');
        }

        // When
        $result = $decoder->decode($binary);

        //Then
        if ($result instanceof Normalizable) {
            static::assertSame($expectedNormalizedValue, $result->normalize());
        }
    }

    public static function getInputs(): iterable
    {
        yield ['00', '0'];
        yield ['01', '1'];
        yield ['0a', '10'];
        yield ['17', '23'];
        yield ['1818', '24'];
        yield ['1819', '25'];
        yield ['1864', '100'];
        yield ['1903e8', '1000'];
        yield ['1a000f4240', '1000000'];
        yield ['1b000000e8d4a51000', '1000000000000'];
        yield ['20', '-1'];
        yield ['29', '-10'];
        yield ['3863', '-100'];
        yield ['3903e7', '-1000'];
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
}
