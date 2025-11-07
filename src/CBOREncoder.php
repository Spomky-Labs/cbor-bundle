<?php

declare(strict_types=1);

namespace SpomkyLabs\CborBundle;

use BackedEnum;
use CBOR\CBORObject;
use CBOR\Encoder;
use CBOR\Normalizable;
use InvalidArgumentException;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Throwable;
use UnitEnum;

/**
 * CBOR Encoder/Decoder for Symfony Serializer component.
 *
 * This encoder implements both EncoderInterface and DecoderInterface
 * to provide CBOR (Concise Binary Object Representation) serialization
 * support in Symfony applications.
 *
 * Supported context options for encoding:
 * - 'cbor_indefinite_text_string' (bool): Use indefinite length for text strings
 * - 'cbor_indefinite_byte_string' (bool): Use indefinite length for byte strings
 * - 'cbor_indefinite_list' (bool): Use indefinite length for lists
 * - 'cbor_indefinite_map' (bool): Use indefinite length for maps
 * - 'cbor_single_precision_float' (bool): Use single precision for floats
 *
 * @see https://www.rfc-editor.org/rfc/rfc8949.html CBOR Specification
 */
final readonly class CBOREncoder implements DecoderInterface, EncoderInterface
{
    public function __construct(
        private CBORDecoder $decoder,
        private Encoder $encoder
    ) {
    }

    /**
     * Decodes CBOR binary data into PHP values.
     *
     * @param string $data The CBOR binary data to decode
     * @param string $format The format being decoded (must be 'cbor')
     * @param array<string, mixed> $context Additional context for decoding (currently unused)
     *
     * @return mixed The decoded PHP value
     *
     * @throws InvalidArgumentException If decoding fails
     */
    public function decode(string $data, string $format, array $context = []): mixed
    {
        try {
            $result = $this->decoder->decode($data);

            return $result instanceof Normalizable ? $result->normalize() : $result;
        } catch (Throwable $e) {
            throw new InvalidArgumentException(
                sprintf('Unable to decode CBOR data: %s', $e->getMessage()),
                0,
                $e
            );
        }
    }

    public function supportsDecoding(string $format): bool
    {
        return $format === 'cbor';
    }

    /**
     * Encodes PHP values into CBOR binary format.
     *
     * Supports encoding of:
     * - Scalars: int, float, string, bool, null
     * - Arrays: indexed arrays (as CBOR lists) and associative arrays (as CBOR maps)
     * - Enums: BackedEnum values are encoded as their backing value, UnitEnum as their name
     * - CBORObject instances: encoded directly
     *
     * @param mixed $data The PHP value to encode
     * @param string $format The format being encoded (must be 'cbor')
     * @param array<string, mixed> $context Encoding options
     *
     * @return string The CBOR binary data
     *
     * @throws InvalidArgumentException If encoding fails or data type is unsupported
     */
    public function encode(mixed $data, string $format, array $context = []): string
    {
        try {
            // If already a CBORObject, convert directly
            if ($data instanceof CBORObject) {
                return (string) $data;
            }

            // Handle enums
            if ($data instanceof BackedEnum) {
                $data = $data->value;
            } elseif ($data instanceof UnitEnum) {
                $data = $data->name;
            }

            // Build encoding options from context
            $options = $this->buildEncodingOptions($context);

            return $this->encoder->encode($data, $options);
        } catch (Throwable $e) {
            throw new InvalidArgumentException(
                sprintf('Unable to encode data to CBOR format: %s', $e->getMessage()),
                0,
                $e
            );
        }
    }

    public function supportsEncoding(string $format): bool
    {
        return $format === 'cbor';
    }

    /**
     * Builds encoding options from the context array.
     *
     * @param array<string, mixed> $context The encoding context
     *
     * @return int Bitwise OR of Encoder constants
     */
    private function buildEncodingOptions(array $context): int
    {
        $options = 0;

        if ($context['cbor_indefinite_text_string'] ?? false) {
            $options |= Encoder::INDEFINITE_TEXT_STRING_LENGTH;
        }

        if ($context['cbor_indefinite_byte_string'] ?? false) {
            $options |= Encoder::INDEFINITE_BYTE_STRING_LENGTH;
        }

        if ($context['cbor_indefinite_list'] ?? false) {
            $options |= Encoder::INDEFINITE_LIST_LENGTH;
        }

        if ($context['cbor_indefinite_map'] ?? false) {
            $options |= Encoder::INDEFINITE_MAP_LENGTH;
        }

        if ($context['cbor_single_precision_float'] ?? false) {
            $options |= Encoder::FLOAT_FORMAT_SINGLE_PRECISION;
        }

        return $options;
    }
}
