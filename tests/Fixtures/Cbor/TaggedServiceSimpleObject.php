<?php

declare(strict_types=1);

namespace SpomkyLabs\CborBundle\Tests\Fixtures\Cbor;

use CBOR\OtherObject;

/**
 * Claims one of the unassigned simple values the library files under SimpleObject, so that decoding it tells
 * whether the class was registered.
 */
final class TaggedServiceSimpleObject extends OtherObject
{
    public const int ADDITIONAL_INFORMATION = 18;

    public static function supportedAdditionalInformation(): array
    {
        return [self::ADDITIONAL_INFORMATION];
    }

    public static function createFromLoadedData(int $additionalInformation, ?string $data): OtherObject
    {
        return new self($additionalInformation, $data);
    }
}
