<?php

declare(strict_types=1);

namespace SpomkyLabs\CborBundle\Tests\Fixtures\Cbor;

use CBOR\CBORObject;
use CBOR\Tag;

/**
 * A tag of the private use range, with the constructor every tag has: the container cannot build it, and must not
 * try to.
 */
final class AutoconfiguredTag extends Tag
{
    public const int TAG_ID = 4002;

    public static function getTagId(): int
    {
        return self::TAG_ID;
    }

    public static function createFromLoadedData(int $additionalInformation, ?string $data, CBORObject $object): Tag
    {
        return new self($additionalInformation, $data, $object);
    }
}
