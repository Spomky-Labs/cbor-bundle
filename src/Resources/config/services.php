<?php

declare(strict_types=1);

use CBOR\Decoder;
use CBOR\OtherObject\BreakObject;
use CBOR\OtherObject\DoublePrecisionFloatObject;
use CBOR\OtherObject\FalseObject;
use CBOR\OtherObject\HalfPrecisionFloatObject;
use CBOR\OtherObject\NullObject;
use CBOR\OtherObject\OtherObjectManager;
use CBOR\OtherObject\SimpleObject;
use CBOR\OtherObject\SinglePrecisionFloatObject;
use CBOR\OtherObject\TrueObject;
use CBOR\OtherObject\UndefinedObject;
use CBOR\Tag\Base16EncodingTag;
use CBOR\Tag\Base64EncodingTag;
use CBOR\Tag\Base64Tag;
use CBOR\Tag\Base64UrlEncodingTag;
use CBOR\Tag\Base64UrlTag;
use CBOR\Tag\BigFloatTag;
use CBOR\Tag\CBOREncodingTag;
use CBOR\Tag\CBORTag;
use CBOR\Tag\DatetimeTag;
use CBOR\Tag\DecimalFractionTag;
use CBOR\Tag\MimeTag;
use CBOR\Tag\NegativeBigIntegerTag;
use CBOR\Tag\TagManager;
use CBOR\Tag\TimestampTag;
use CBOR\Tag\UnsignedBigIntegerTag;
use CBOR\Tag\UriTag;
use SpomkyLabs\CborBundle\CBORDecoder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $container = $container->services()
        ->defaults()
        ->private()
        ->autoconfigure()
        ->autowire()
    ;

    $container->set(Decoder::class)->public();
    $container->set(CBORDecoder::class)->public();

    $container->set(OtherObjectManager::class)
        ->call('add', [BreakObject::class])
        ->call('add', [DoublePrecisionFloatObject::class])
        ->call('add', [FalseObject::class])
        ->call('add', [HalfPrecisionFloatObject::class])
        ->call('add', [NullObject::class])
        ->call('add', [SimpleObject::class])
        ->call('add', [SinglePrecisionFloatObject::class])
        ->call('add', [TrueObject::class])
        ->call('add', [UndefinedObject::class])
        ->private()
    ;

    $container->set(TagManager::class)
        ->call('add', [Base16EncodingTag::class])
        ->call('add', [Base64EncodingTag::class])
        ->call('add', [Base64Tag::class])
        ->call('add', [Base64UrlEncodingTag::class])
        ->call('add', [Base64UrlTag::class])
        ->call('add', [BigFloatTag::class])
        ->call('add', [CBOREncodingTag::class])
        ->call('add', [CBORTag::class])
        ->call('add', [DatetimeTag::class])
        ->call('add', [DecimalFractionTag::class])
        ->call('add', [MimeTag::class])
        ->call('add', [NegativeBigIntegerTag::class])
        ->call('add', [TimestampTag::class])
        ->call('add', [UnsignedBigIntegerTag::class])
        ->call('add', [UriTag::class])
        ->private()
    ;
};
