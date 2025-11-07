<?php

declare(strict_types=1);

namespace SpomkyLabs\CborBundle;

use CBOR\CBORObject;
use CBOR\Encoder;
use CBOR\Normalizable;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;

final readonly class CBOREncoder implements DecoderInterface, EncoderInterface
{
    public function __construct(
        private CBORDecoder $decoder,
        private Encoder $encoder
    ) {
    }

    public function decode(string $data, string $format, array $context = []): mixed
    {
        $result = $this->decoder->decode($data);

        return $result instanceof Normalizable ? $result->normalize() : $result;
    }

    public function supportsDecoding(string $format): bool
    {
        return $format === 'cbor';
    }

    public function encode(mixed $data, string $format, array $context = []): string
    {
        if ($data instanceof CBORObject) {
            return (string) $data;
        }

        return $this->encoder->encode($data);
    }

    public function supportsEncoding(string $format): bool
    {
        return $format === 'cbor';
    }
}
