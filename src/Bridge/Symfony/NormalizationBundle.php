<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalization\Bridge\Symfony;

use MakinaCorpus\Normalization\Alias;
use MakinaCorpus\Normalization\DomainAlias;
use MakinaCorpus\Normalization\Bridge\Symfony\DependencyInjection\NormalizationExtension;
use MakinaCorpus\Normalization\Bridge\Symfony\DependencyInjection\Compiler\RegisterStaticNameMapPass;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @codeCoverageIgnore
 */
final class NormalizationBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new RegisterStaticNameMapPass());

        $container->registerAttributeForAutoconfiguration(
            Alias::class,
            static function (
                ChildDefinition $definition,
                Alias $attribute
                /* \ReflectionClass|\ReflectionMethod $reflector */
            ): void {
                $definition->addTag('normalization.aliased');
            }
        );

        $container->registerAttributeForAutoconfiguration(
            DomainAlias::class,
            static function (
                ChildDefinition $definition,
                DomainAlias $attribute
                /* \ReflectionClass|\ReflectionMethod $reflector */
            ): void {
                $definition->addTag('normalization.aliased');
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new NormalizationExtension();
    }
}
