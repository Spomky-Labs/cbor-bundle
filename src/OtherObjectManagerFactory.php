<?php

declare(strict_types=1);

namespace SpomkyLabs\CborBundle;

use CBOR\OtherObject\BreakObject;
use CBOR\OtherObject\DoublePrecisionFloatObject;
use CBOR\OtherObject\FalseObject;
use CBOR\OtherObject\HalfPrecisionFloatObject;
use CBOR\OtherObject\NullObject;
use CBOR\OtherObject\OtherObjectInterface;
use CBOR\OtherObject\OtherObjectManager;
use CBOR\OtherObject\SimpleObject;
use CBOR\OtherObject\SinglePrecisionFloatObject;
use CBOR\OtherObject\TrueObject;
use CBOR\OtherObject\UndefinedObject;

/**
 * Builds the manager of major type 7 items (simple values, floats and the break code) the bundle hands to the
 * decoder.
 *
 * The list mirrors the one the library uses when it builds a decoder without a manager, and a test checks the two
 * agree.
 */
final class OtherObjectManagerFactory
{
    /**
     * @var list<class-string<OtherObjectInterface>>
     */
    public const array OTHER_OBJECTS = [
        BreakObject::class,
        SimpleObject::class,
        FalseObject::class,
        TrueObject::class,
        NullObject::class,
        UndefinedObject::class,
        HalfPrecisionFloatObject::class,
        SinglePrecisionFloatObject::class,
        DoublePrecisionFloatObject::class,
    ];

    public static function create(): OtherObjectManager
    {
        return OtherObjectManager::create(self::OTHER_OBJECTS);
    }
}
