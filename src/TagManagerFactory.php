<?php

declare(strict_types=1);

namespace SpomkyLabs\CborBundle;

use CBOR\CBORObject;
use CBOR\Tag\Base16EncodingTag;
use CBOR\Tag\Base64EncodingTag;
use CBOR\Tag\Base64Tag;
use CBOR\Tag\Base64UrlEncodingTag;
use CBOR\Tag\Base64UrlTag;
use CBOR\Tag\BigFloatTag;
use CBOR\Tag\BinaryMimeTag;
use CBOR\Tag\CBOREncodingTag;
use CBOR\Tag\CBORSequenceTag;
use CBOR\Tag\CBORTag;
use CBOR\Tag\ColumnMajorMultiDimensionalArrayTag;
use CBOR\Tag\CoseEncrypt0Tag;
use CBOR\Tag\CoseEncryptTag;
use CBOR\Tag\CoseMac0Tag;
use CBOR\Tag\CoseMacTag;
use CBOR\Tag\CoseSign1Tag;
use CBOR\Tag\CoseSignTag;
use CBOR\Tag\CwtTag;
use CBOR\Tag\DateStringTag;
use CBOR\Tag\DateTag;
use CBOR\Tag\DatetimeTag;
use CBOR\Tag\DecimalFractionTag;
use CBOR\Tag\DurationTag;
use CBOR\Tag\ExplicitMapTag;
use CBOR\Tag\ExtendedTimeTag;
use CBOR\Tag\HomogeneousArrayTag;
use CBOR\Tag\IdentifierTag;
use CBOR\Tag\IpldContentIdentifierTag;
use CBOR\Tag\Ipv4Tag;
use CBOR\Tag\Ipv6Tag;
use CBOR\Tag\LanguageIndependentObjectTag;
use CBOR\Tag\LanguageTaggedStringTag;
use CBOR\Tag\MimeTag;
use CBOR\Tag\NegativeBigIntegerTag;
use CBOR\Tag\NetworkAddressPrefixTag;
use CBOR\Tag\NetworkAddressTag;
use CBOR\Tag\PeriodTag;
use CBOR\Tag\PerlObjectTag;
use CBOR\Tag\RationalNumberTag;
use CBOR\Tag\RegexpTag;
use CBOR\Tag\RowMajorMultiDimensionalArrayTag;
use CBOR\Tag\SetTag;
use CBOR\Tag\ShareableTag;
use CBOR\Tag\SharedReferenceTag;
use CBOR\Tag\StringReferenceNamespaceTag;
use CBOR\Tag\StringReferenceTag;
use CBOR\Tag\TagInterface;
use CBOR\Tag\TagManager;
use CBOR\Tag\TimestampTag;
use CBOR\Tag\TypedArray\Float128BigEndianArrayTag;
use CBOR\Tag\TypedArray\Float128LittleEndianArrayTag;
use CBOR\Tag\TypedArray\Float16BigEndianArrayTag;
use CBOR\Tag\TypedArray\Float16LittleEndianArrayTag;
use CBOR\Tag\TypedArray\Float32BigEndianArrayTag;
use CBOR\Tag\TypedArray\Float32LittleEndianArrayTag;
use CBOR\Tag\TypedArray\Float64BigEndianArrayTag;
use CBOR\Tag\TypedArray\Float64LittleEndianArrayTag;
use CBOR\Tag\TypedArray\Sint16BigEndianArrayTag;
use CBOR\Tag\TypedArray\Sint16LittleEndianArrayTag;
use CBOR\Tag\TypedArray\Sint32BigEndianArrayTag;
use CBOR\Tag\TypedArray\Sint32LittleEndianArrayTag;
use CBOR\Tag\TypedArray\Sint64BigEndianArrayTag;
use CBOR\Tag\TypedArray\Sint64LittleEndianArrayTag;
use CBOR\Tag\TypedArray\Sint8ArrayTag;
use CBOR\Tag\TypedArray\Uint16BigEndianArrayTag;
use CBOR\Tag\TypedArray\Uint16LittleEndianArrayTag;
use CBOR\Tag\TypedArray\Uint32BigEndianArrayTag;
use CBOR\Tag\TypedArray\Uint32LittleEndianArrayTag;
use CBOR\Tag\TypedArray\Uint64BigEndianArrayTag;
use CBOR\Tag\TypedArray\Uint64LittleEndianArrayTag;
use CBOR\Tag\TypedArray\Uint8ArrayTag;
use CBOR\Tag\TypedArray\Uint8ClampedArrayTag;
use CBOR\Tag\UnsignedBigIntegerTag;
use CBOR\Tag\UriTag;
use CBOR\Tag\UuidTag;

/**
 * Builds the tag manager the bundle hands to the decoder.
 *
 * The manager starts with every tag class the library implements from the IANA registry, filed by tag number
 * through TagManager::register() so that no class is loaded before a document mentions it. The list mirrors the
 * one the library uses when it builds a decoder without a manager; it has to be repeated here because the library
 * keeps its own private, and a test checks the two agree.
 */
final class TagManagerFactory
{
    /**
     * @var array<int, class-string<TagInterface>>
     */
    public const array TAGS = [
        CBORObject::TAG_STANDARD_DATETIME => DatetimeTag::class,
        CBORObject::TAG_EPOCH_DATETIME => TimestampTag::class,
        CBORObject::TAG_UNSIGNED_BIG_NUM => UnsignedBigIntegerTag::class,
        CBORObject::TAG_NEGATIVE_BIG_NUM => NegativeBigIntegerTag::class,
        CBORObject::TAG_DECIMAL_FRACTION => DecimalFractionTag::class,
        CBORObject::TAG_BIG_FLOAT => BigFloatTag::class,
        CBORObject::TAG_COSE_ENCRYPT0 => CoseEncrypt0Tag::class,
        CBORObject::TAG_COSE_MAC0 => CoseMac0Tag::class,
        CBORObject::TAG_COSE_SIGN1 => CoseSign1Tag::class,
        CBORObject::TAG_ENCODED_BASE64_URL => Base64UrlEncodingTag::class,
        CBORObject::TAG_ENCODED_BASE64 => Base64EncodingTag::class,
        CBORObject::TAG_ENCODED_BASE16 => Base16EncodingTag::class,
        CBORObject::TAG_ENCODED_CBOR => CBOREncodingTag::class,
        CBORObject::TAG_STRING_REFERENCE => StringReferenceTag::class,
        CBORObject::TAG_PERL_OBJECT => PerlObjectTag::class,
        CBORObject::TAG_LANGUAGE_INDEPENDENT_OBJECT => LanguageIndependentObjectTag::class,
        CBORObject::TAG_SHAREABLE => ShareableTag::class,
        CBORObject::TAG_SHARED_REFERENCE => SharedReferenceTag::class,
        CBORObject::TAG_RATIONAL_NUMBER => RationalNumberTag::class,
        CBORObject::TAG_URI => UriTag::class,
        CBORObject::TAG_BASE64_URL => Base64UrlTag::class,
        CBORObject::TAG_BASE64 => Base64Tag::class,
        CBORObject::TAG_REGULAR_EXPRESSION => RegexpTag::class,
        CBORObject::TAG_MIME => MimeTag::class,
        CBORObject::TAG_UUID => UuidTag::class,
        CBORObject::TAG_LANGUAGE_TAGGED_STRING => LanguageTaggedStringTag::class,
        CBORObject::TAG_IDENTIFIER => IdentifierTag::class,
        CBORObject::TAG_ROW_MAJOR_MULTI_DIMENSIONAL_ARRAY => RowMajorMultiDimensionalArrayTag::class,
        CBORObject::TAG_HOMOGENEOUS_ARRAY => HomogeneousArrayTag::class,
        CBORObject::TAG_IPLD_CONTENT_IDENTIFIER => IpldContentIdentifierTag::class,
        CBORObject::TAG_IPV4 => Ipv4Tag::class,
        CBORObject::TAG_IPV6 => Ipv6Tag::class,
        CBORObject::TAG_CWT => CwtTag::class,
        CBORObject::TAG_ENCODED_CBOR_SEQUENCE => CBORSequenceTag::class,
        CBORObject::TAG_TYPED_ARRAY_UINT8 => Uint8ArrayTag::class,
        CBORObject::TAG_TYPED_ARRAY_UINT16_BE => Uint16BigEndianArrayTag::class,
        CBORObject::TAG_TYPED_ARRAY_UINT32_BE => Uint32BigEndianArrayTag::class,
        CBORObject::TAG_TYPED_ARRAY_UINT64_BE => Uint64BigEndianArrayTag::class,
        CBORObject::TAG_TYPED_ARRAY_UINT8_CLAMPED => Uint8ClampedArrayTag::class,
        CBORObject::TAG_TYPED_ARRAY_UINT16_LE => Uint16LittleEndianArrayTag::class,
        CBORObject::TAG_TYPED_ARRAY_UINT32_LE => Uint32LittleEndianArrayTag::class,
        CBORObject::TAG_TYPED_ARRAY_UINT64_LE => Uint64LittleEndianArrayTag::class,
        CBORObject::TAG_TYPED_ARRAY_SINT8 => Sint8ArrayTag::class,
        CBORObject::TAG_TYPED_ARRAY_SINT16_BE => Sint16BigEndianArrayTag::class,
        CBORObject::TAG_TYPED_ARRAY_SINT32_BE => Sint32BigEndianArrayTag::class,
        CBORObject::TAG_TYPED_ARRAY_SINT64_BE => Sint64BigEndianArrayTag::class,
        CBORObject::TAG_TYPED_ARRAY_SINT16_LE => Sint16LittleEndianArrayTag::class,
        CBORObject::TAG_TYPED_ARRAY_SINT32_LE => Sint32LittleEndianArrayTag::class,
        CBORObject::TAG_TYPED_ARRAY_SINT64_LE => Sint64LittleEndianArrayTag::class,
        CBORObject::TAG_TYPED_ARRAY_FLOAT16_BE => Float16BigEndianArrayTag::class,
        CBORObject::TAG_TYPED_ARRAY_FLOAT32_BE => Float32BigEndianArrayTag::class,
        CBORObject::TAG_TYPED_ARRAY_FLOAT64_BE => Float64BigEndianArrayTag::class,
        CBORObject::TAG_TYPED_ARRAY_FLOAT128_BE => Float128BigEndianArrayTag::class,
        CBORObject::TAG_TYPED_ARRAY_FLOAT16_LE => Float16LittleEndianArrayTag::class,
        CBORObject::TAG_TYPED_ARRAY_FLOAT32_LE => Float32LittleEndianArrayTag::class,
        CBORObject::TAG_TYPED_ARRAY_FLOAT64_LE => Float64LittleEndianArrayTag::class,
        CBORObject::TAG_TYPED_ARRAY_FLOAT128_LE => Float128LittleEndianArrayTag::class,
        CBORObject::TAG_COSE_ENCRYPT => CoseEncryptTag::class,
        CBORObject::TAG_COSE_MAC => CoseMacTag::class,
        CBORObject::TAG_COSE_SIGN => CoseSignTag::class,
        CBORObject::TAG_DATE => DateTag::class,
        CBORObject::TAG_STRING_REFERENCE_NAMESPACE => StringReferenceNamespaceTag::class,
        CBORObject::TAG_BINARY_MIME => BinaryMimeTag::class,
        CBORObject::TAG_SET => SetTag::class,
        CBORObject::TAG_EXPLICIT_MAP => ExplicitMapTag::class,
        CBORObject::TAG_NETWORK_ADDRESS => NetworkAddressTag::class,
        CBORObject::TAG_NETWORK_ADDRESS_PREFIX => NetworkAddressPrefixTag::class,
        CBORObject::TAG_EXTENDED_TIME => ExtendedTimeTag::class,
        CBORObject::TAG_DURATION => DurationTag::class,
        CBORObject::TAG_PERIOD => PeriodTag::class,
        CBORObject::TAG_DATE_STRING => DateStringTag::class,
        CBORObject::TAG_COLUMN_MAJOR_MULTI_DIMENSIONAL_ARRAY => ColumnMajorMultiDimensionalArrayTag::class,
        CBORObject::TAG_CBOR => CBORTag::class,
    ];

    public static function create(): TagManager
    {
        $manager = TagManager::create();
        foreach (self::TAGS as $tagId => $class) {
            $manager->register($tagId, $class);
        }

        return $manager;
    }
}
