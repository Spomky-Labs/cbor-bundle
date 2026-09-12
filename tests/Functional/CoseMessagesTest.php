<?php

declare(strict_types=1);

namespace SpomkyLabs\CborBundle\Tests\Functional;

use CBOR\Decoder;
use CBOR\Tag\AbstractCoseTag;
use CBOR\Tag\CoseEncrypt0Tag;
use CBOR\Tag\CoseEncryptTag;
use CBOR\Tag\CoseMac0Tag;
use CBOR\Tag\CoseMacTag;
use CBOR\Tag\CoseSign1Tag;
use CBOR\Tag\CoseSignTag;
use CBOR\Tag\CwtTag;
use function count;
use function dirname;
use function file_get_contents;
use function glob;
use function hex2bin;
use function is_array;
use function is_string;
use function json_decode;
use const JSON_THROW_ON_ERROR;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use SpomkyLabs\CborBundle\CBORDecoder;
use function sprintf;
use function strlen;
use function substr;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * The COSE messages of the appendices of RFC 8152 and RFC 8392, as published by the IETF COSE working group, read
 * by the decoder service of the bundle.
 *
 * Verifying them is the job of a COSE library; what is checked here is that the bundle's decoder gives them the tag
 * class the library implements for their type, that the message written back from that object is the one that was
 * read, and that the protected header can be opened with the same decoder.
 *
 * @internal
 */
final class CoseMessagesTest extends KernelTestCase
{
    /**
     * The member of "input" that carries each message type, and the class its tag decodes to.
     *
     * @var array<string, class-string<AbstractCoseTag>>
     */
    private const array MESSAGE_CLASSES = [
        'sign' => CoseSignTag::class,
        'sign0' => CoseSign1Tag::class,
        'mac' => CoseMacTag::class,
        'mac0' => CoseMac0Tag::class,
        'enveloped' => CoseEncryptTag::class,
        'encrypted' => CoseEncrypt0Tag::class,
    ];

    /**
     * @param class-string<AbstractCoseTag> $class
     */
    #[Test]
    #[DataProvider('messages')]
    public function aCoseMessageIsDecodedIntoItsTagClassAndWrittenBackUnchanged(
        string $message,
        string $class,
        int $protectedHeaderSize
    ): void {
        // Given
        static::bootKernel();
        /** @var CBORDecoder $decoder */
        $decoder = static::getContainer()->get(CBORDecoder::class);
        /** @var Decoder $libraryDecoder */
        $libraryDecoder = static::getContainer()->get(Decoder::class);

        // When
        $result = $decoder->decode($message);

        // Then
        static::assertInstanceOf($class, $result);
        static::assertSame($message, (string) $result);
        static::assertCount($protectedHeaderSize, $result->getProtectedHeaderAsMap($libraryDecoder));
    }

    #[Test]
    public function aCborWebTokenIsDecodedIntoTheCwtTagWrappingItsCoseMessage(): void
    {
        // Given
        static::bootKernel();
        /** @var CBORDecoder $decoder */
        $decoder = static::getContainer()->get(CBORDecoder::class);
        // RFC 8392 appendix A.3, wrapped in tag 61 as section 6 allows.
        $message = "\xd9\x00\x3d" . self::fixture(__DIR__ . '/../Fixtures/cose-wg/CWT/A_3.json')['message'];

        // When
        $result = $decoder->decode($message);

        // Then
        static::assertInstanceOf(CwtTag::class, $result);
        static::assertInstanceOf(CoseSign1Tag::class, $result->getValue());
        static::assertSame($message, (string) $result);
    }

    /**
     * @return iterable<string, array{string, class-string<AbstractCoseTag>, int}>
     */
    public static function messages(): iterable
    {
        $directory = dirname(__DIR__) . '/Fixtures/cose-wg';
        foreach (glob($directory . '/*/*.json') ?: [] as $file) {
            $fixture = self::fixture($file);

            yield substr($file, strlen($directory) + 1) . ': ' . $fixture['title'] => [
                $fixture['message'],
                $fixture['class'],
                $fixture['protectedHeaderSize'],
            ];
        }
    }

    /**
     * @return array{title: string, message: string, class: class-string<AbstractCoseTag>, protectedHeaderSize: int}
     */
    private static function fixture(string $file): array
    {
        $content = file_get_contents($file);
        if (! is_string($content)) {
            throw new RuntimeException(sprintf('Unable to read "%s".', $file));
        }
        $fixture = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        if (! is_array($fixture) || ! is_array($fixture['input'] ?? null) || ! is_array($fixture['output'] ?? null)) {
            throw new RuntimeException(sprintf('"%s" is not a cose-wg fixture.', $file));
        }
        foreach (self::MESSAGE_CLASSES as $type => $class) {
            if (! is_array($fixture['input'][$type] ?? null)) {
                continue;
            }
            $message = hex2bin((string) $fixture['output']['cbor']);
            if (! is_string($message)) {
                throw new RuntimeException(sprintf('"%s" carries no CBOR message.', $file));
            }
            $protectedHeader = $fixture['input'][$type]['protected'] ?? [];

            return [
                'title' => (string) $fixture['title'],
                'message' => $message,
                'class' => $class,
                'protectedHeaderSize' => is_array($protectedHeader) ? count($protectedHeader) : 0,
            ];
        }

        throw new RuntimeException(sprintf('"%s" carries no COSE message.', $file));
    }
}
