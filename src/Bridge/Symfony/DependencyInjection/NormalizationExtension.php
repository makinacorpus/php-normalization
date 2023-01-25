<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalization\Bridge\Symfony\DependencyInjection;

use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class NormalizationExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(\dirname(__DIR__).'/Resources/config'));

        $loader->load('normalization.yaml');
        $this->processNormalization($container, $config);

        if (\class_exists(Command::class)) {
            $loader->load('console.yaml');
        }

        if (\interface_exists(UuidInterface::class)) {
            $loader->load('normalization.ramsey_uuid.yaml');
        }
    }

    /**
     * Process type normalization map and aliases.
     */
    private function processNormalization(ContainerBuilder $container, array $config): void
    {
        $container->getDefinition('normalization.name_map.strategy.prefix')->setArguments([
            $config['default_strategy']['app_name'] ?? null,
            $config['default_strategy']['class_prefix'] ?? 'App',
        ]);

        foreach (($config['strategy'] ?? []) as $tag => $serviceId) {
            $container->getDefinition('normalization.name_map')->addMethodCall('setNameMappingStrategy', [new Reference($serviceId), $tag]);
        }
        // Because this configuration will be merged from one from class
        // attributes, this need to be processed in a compiler pass, hence
        // the parameter being here.
        if (!empty($config['static'])) {
            $container->setParameter('normalization.name_map.static.config', $config['static']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new NormalizationConfiguration();
    }
}
