<?php

declare(strict_types=1);

namespace SpomkyLabs\CborBundle;

use CBOR\CBORObject;
use CBOR\Decoder;
use CBOR\StringStream;

class CBORDecoder
{
    public function __construct(
        private Decoder $decoder
    ) {
    }

    public function decode(string $data): CBORObject
    {
        $stream = new StringStream($data);

        return $this->decoder->decode($stream);
    }
}
