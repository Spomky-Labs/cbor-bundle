<?php

declare(strict_types=1);

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2018 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace SpomkyLabs\CborBundle\Tests;

use CBOR\Decoder;
use CBOR\StringStream;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class DecodingTest extends KernelTestCase
{
    /**
     * @test
     */
    public function theDecoderServiceIsAvailable(): void
    {
        static::bootKernel();
        static::assertTrue(static::$kernel->getContainer()->has(Decoder::class));
    }

    /**
     * @test
     * @depends theDecoderServiceIsAvailable
     * @dataProvider getInputs
     */
    public function theDecoderCanDecodeInputs(string $data, $expectedNormalizedValue): void
    {
        static::bootKernel();

        /** @var Decoder $decoder */
        $decoder = static::$kernel->getContainer()->get(Decoder::class);
        $stream = new StringStream(hex2bin($data));

        $result = $decoder->decode($stream);
        static::assertEquals($expectedNormalizedValue, $result->getNormalizedData());
    }

    public function getInputs(): array
    {
        return [
            ['00', '0'],
            ['01', '1'],
            ['0a', '10'],
            ['17', '23'],
            ['1818', '24'],
            ['1819', '25'],
            ['1864', '100'],
            ['1903e8', '1000'],
            ['1a000f4240', '1000000'],
            ['1b000000e8d4a51000', '1000000000000'],
            ['20', '-1'],
            ['29', '-10'],
            ['3863', '-100'],
            ['3903e7', '-1000'],
            ['c349010000000000000000', '-18446744073709551617'],
            ['3bffffffffffffffff', '-18446744073709551616'],
            ['1bffffffffffffffff', '18446744073709551615'],
            ['c249010000000000000000', '18446744073709551616'],
            ['7f624865626c6c616fff', 'Hello'],
            ['7f612863efbda163e2979563e280bf63e2979563efbda16129ff', '(｡◕‿◕｡)'],
            ['6548656c6c6f', 'Hello'],
            ['7128efbda1e29795e280bfe29795efbda129', '(｡◕‿◕｡)'],
            ['781948656c6c6f48656c6c6f48656c6c6f48656c6c6f48656c6c6f', 'HelloHelloHelloHelloHello'],
            ['5f424865426c6c416fff', 'Hello'],
            ['5f412843efbda143e2979543e280bf43e2979543efbda14129ff', '(｡◕‿◕｡)'],
            ['4548656c6c6f', 'Hello'],
            ['5128efbda1e29795e280bfe29795efbda129', '(｡◕‿◕｡)'],
            ['581948656c6c6f48656c6c6f48656c6c6f48656c6c6f48656c6c6f', 'HelloHelloHelloHelloHello'],
            ['fb3fd5555555555555', 1 / 3],
        ];
    }
}
