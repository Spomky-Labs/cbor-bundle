<?php

declare(strict_types=1);

namespace SpomkyLabs\CborBundle\Tests\Unit;

use CBOR\Decoder;
use CBOR\OtherObject\OtherObjectManager;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use SpomkyLabs\CborBundle\OtherObjectManagerFactory;

/**
 * @internal
 */
final class OtherObjectManagerFactoryTest extends TestCase
{
    /**
     * The library builds its default manager in a private method, so the bundle repeats the list. Major type 7 has
     * 32 additional information values; the two managers have to answer the same class for each of them.
     */
    #[Test]
    public function theManagerMirrorsTheOneOfTheLibrary(): void
    {
        // Given
        $expected = (new ReflectionProperty(Decoder::class, 'otherTypeManager'))->getValue(Decoder::create());
        static::assertInstanceOf(OtherObjectManager::class, $expected);

        // When
        $actual = OtherObjectManagerFactory::create();

        // Then
        for ($additionalInformation = 0; $additionalInformation < 32; ++$additionalInformation) {
            static::assertSame(
                $expected->getClassForValue($additionalInformation),
                $actual->getClassForValue($additionalInformation),
                (string) $additionalInformation
            );
        }
    }
}
