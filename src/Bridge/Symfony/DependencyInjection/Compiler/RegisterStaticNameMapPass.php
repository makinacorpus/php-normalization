<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalization\Bridge\Symfony\DependencyInjection\Compiler;

use MakinaCorpus\Normalization\Builder\NameMapBuilder;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

final class RegisterStaticNameMapPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('normalization.name_map.static')) {
            return;
        }

        $builder = new NameMapBuilder();

        foreach ($container->findTaggedServiceIds('normalization.aliased', true) as $id => $attributes) {
            $definition = $container->getDefinition($id);
            $className = $definition->getClass();
            if (!$container->getReflectionClass($className)) {
                throw new InvalidArgumentException(\sprintf('Class "%s" used for service "%s" cannot be found.', $className, $id));
            }
            $builder->addFromClass($className);
        }

        if ($container->hasParameter('normalization.name_map.static.config')) {
            if ($config = $container->getParameter('normalization.name_map.static.config')) {
                $builder->addFromLegacyConfig($config);
            }
        }

        $container
            ->getDefinition('normalization.name_map.static')
            ->setArguments([
                $builder->getLogicalToPhpNameMap(),
                $builder->getPhpToLogicalNameMap(),
            ])
        ;
    }
}
