<?php

declare(strict_types=1);

namespace SpomkyLabs\CborBundle\Tests\Unit;

use CBOR\OtherObject\OtherObjectManager;
use CBOR\Tag\TagManager;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use SpomkyLabs\CborBundle\DependencyInjection\Compiler\OtherObjectCompilerPass;
use SpomkyLabs\CborBundle\DependencyInjection\Compiler\TagCompilerPass;
use SpomkyLabs\CborBundle\Tests\Fixtures\Cbor\ConfiguredSimpleObject;
use SpomkyLabs\CborBundle\Tests\Fixtures\Cbor\ConfiguredTag;
use stdClass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

/**
 * @internal
 */
final class CompilerPassTest extends TestCase
{
    #[Test]
    public function aTaggedServiceIsFiledByItsClassAndTagNumber(): void
    {
        // Given
        $container = new ContainerBuilder();
        $container->register(TagManager::class);
        $container->register('app.tag', ConfiguredTag::class)->addTag(TagCompilerPass::TAG);

        // When
        (new TagCompilerPass())->process($container);

        // Then
        static::assertSame(
            [['register', [ConfiguredTag::TAG_ID, ConfiguredTag::class]]],
            $container->getDefinition(TagManager::class)->getMethodCalls()
        );
    }

    #[Test]
    public function aTaggedServiceWhoseClassIsNotATagIsRejected(): void
    {
        // Given
        $container = new ContainerBuilder();
        $container->register(TagManager::class);
        $container->register('app.not_a_tag', stdClass::class)->addTag(TagCompilerPass::TAG);

        // Then
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The service "app.not_a_tag" is tagged "cbor.tag" but its class does not implement');

        // When
        (new TagCompilerPass())->process($container);
    }

    #[Test]
    public function aTaggedOtherObjectServiceIsFiledByItsClass(): void
    {
        // Given
        $container = new ContainerBuilder();
        $container->register(OtherObjectManager::class);
        $container->register('app.object', ConfiguredSimpleObject::class)->addTag(OtherObjectCompilerPass::TAG);

        // When
        (new OtherObjectCompilerPass())->process($container);

        // Then
        static::assertSame(
            [['add', [ConfiguredSimpleObject::class]]],
            $container->getDefinition(OtherObjectManager::class)->getMethodCalls()
        );
    }

    #[Test]
    public function aTaggedServiceWhoseClassIsNotAnOtherObjectIsRejected(): void
    {
        // Given
        $container = new ContainerBuilder();
        $container->register(OtherObjectManager::class);
        $container->register('app.not_an_object', stdClass::class)->addTag(OtherObjectCompilerPass::TAG);

        // Then
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The service "app.not_an_object" is tagged "cbor.other_object" but its class does not implement'
        );

        // When
        (new OtherObjectCompilerPass())->process($container);
    }

    #[Test]
    public function nothingHappensWithoutTheManagers(): void
    {
        // Given
        $container = new ContainerBuilder();
        $container->register('app.tag', ConfiguredTag::class)->addTag(TagCompilerPass::TAG);
        $container->register('app.object', ConfiguredSimpleObject::class)->addTag(OtherObjectCompilerPass::TAG);

        // When
        (new TagCompilerPass())->process($container);
        (new OtherObjectCompilerPass())->process($container);

        // Then
        static::assertFalse($container->hasDefinition(TagManager::class));
        static::assertFalse($container->hasDefinition(OtherObjectManager::class));
    }
}
