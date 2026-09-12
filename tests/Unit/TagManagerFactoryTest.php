<?php

declare(strict_types=1);

namespace SpomkyLabs\CborBundle\Tests\Unit;

use CBOR\Decoder;
use function ksort;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClassConstant;
use SpomkyLabs\CborBundle\TagManagerFactory;

/**
 * @internal
 */
final class TagManagerFactoryTest extends TestCase
{
    /**
     * The library keeps its default registry private, so the bundle repeats it. This is the check that the copy has
     * not fallen behind: when it fails, a tag was added to (or removed from) the library and TagManagerFactory::TAGS
     * has to follow.
     */
    #[Test]
    public function theRegistryMirrorsTheOneOfTheLibrary(): void
    {
        // Given
        /** @var array<int, class-string> $expected */
        $expected = (new ReflectionClassConstant(Decoder::class, 'DEFAULT_TAGS'))->getValue();
        $actual = TagManagerFactory::TAGS;
        ksort($expected);
        ksort($actual);

        // Then
        static::assertSame($expected, $actual);
    }

    /**
     * register() files a class without asking it for its number, so nothing else checks the two agree.
     */
    #[Test]
    public function everyClassAgreesWithTheNumberItIsFiledUnder(): void
    {
        foreach (TagManagerFactory::TAGS as $tagId => $class) {
            static::assertSame($tagId, $class::getTagId(), $class);
        }
    }

    #[Test]
    public function theManagerAnswersEveryRegisteredClass(): void
    {
        // When
        $manager = TagManagerFactory::create();

        // Then
        foreach (TagManagerFactory::TAGS as $tagId => $class) {
            static::assertSame($class, $manager->getClassForValue($tagId));
        }
    }
}
