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

use CBOR\Decoder;
use CBOR\OtherObject;
use CBOR\Tag;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $container) {
    $container = $container->services()->defaults()
        ->private()
        ->autoconfigure()
        ->autowire();

    $container->set(Decoder::class)
        ->public();

    $container->set(OtherObject\OtherObjectManager::class)
        ->call('add', [OtherObject\BreakObject::class])
        ->call('add', [OtherObject\DoublePrecisionFloatObject::class])
        ->call('add', [OtherObject\FalseObject::class])
        ->call('add', [OtherObject\HalfPrecisionFloatObject::class])
        ->call('add', [OtherObject\NullObject::class])
        ->call('add', [OtherObject\SimpleObject::class])
        ->call('add', [OtherObject\SinglePrecisionFloatObject::class])
        ->call('add', [OtherObject\UndefinedObject::class])
        ->private();

    $container->set(Tag\TagObjectManager::class)
        ->call('add', [Tag\Base16EncodingTag::class])
        ->call('add', [Tag\Base64EncodingTag::class])
        ->call('add', [Tag\Base64UrlEncodingTag::class])
        ->call('add', [Tag\BigFloatTag::class])
        ->call('add', [Tag\DecimalFractionTag::class])
        ->call('add', [Tag\EpochTag::class])
        ->call('add', [Tag\NegativeBigIntegerTag::class])
        ->call('add', [Tag\PositiveBigIntegerTag::class])
        ->call('add', [Tag\TimestampTag::class])
        ->private();
};
