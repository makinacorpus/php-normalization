<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalization\Bridge\Symfony\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

final class NormalizationConfiguration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('normalization');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->arrayNode('default_strategy')
                    ->children()
                        ->scalarNode('app_name')->defaultValue(null)->end()
                        ->scalarNode('class_prefix')->defaultValue('App')->end()
                    ->end()
                ->end()
                ->variableNode('strategy')->end()
                ->arrayNode('static')
                    ->normalizeKeys(true)
                    ->prototype('array')
                        ->children()
                            ->variableNode('map')->end()
                            ->variableNode('aliases')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
