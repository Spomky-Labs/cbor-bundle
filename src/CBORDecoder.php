<?php

declare(strict_types=1);

namespace SpomkyLabs\CborBundle;

use CBOR\CBORObject;
use CBOR\Decoder;
use CBOR\StringStream;

/**
 * Low-level CBOR decoder wrapper.
 *
 * This class provides a simple wrapper around the CBOR decoder
 * to decode binary CBOR data into CBORObject instances.
 *
 * For higher-level decoding that integrates with Symfony's Serializer
 * component and returns normalized PHP values, use CBOREncoder instead.
 *
 * @see CBOREncoder For Symfony Serializer integration
 */
final readonly class CBORDecoder
{
    public function __construct(
        private Decoder $decoder
    ) {
    }

    /**
     * Decodes CBOR binary data into a CBORObject.
     *
     * @param string $data The CBOR binary data to decode
     *
     * @return CBORObject The decoded CBOR object
     */
    public function decode(string $data): CBORObject
    {
        $stream = new StringStream($data);

        return $this->decoder->decode($stream);
    }
}
