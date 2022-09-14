<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalization\Bridge\Symfony\DependencyInjection;

use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
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
     * Normalize type name, do not check for type existence?
     *
     * @codeCoverageIgnore
     * @todo Export this into a testable class.
     */
    private function normalizeType(string $type, string $key): string
    {
        if (!\is_string($type)) {
            throw new InvalidArgumentException(\sprintf(
                "normalization.map: key '%s': value must be a string",
                $key
            ));
        }
        if (\ctype_digit($key)) {
            throw new InvalidArgumentException(\sprintf(
                "normalization.map: key '%s': cannot be numeric",
                $key
            ));
        }
        // Normalize to FQDN
        return \ltrim(\trim($type), '\\');
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
        foreach (($config['static'] ?? []) as $tag => $data) {
            $this->processNormalizationStaticForContext($container, $tag, $data['map'] ?? [], $data['aliases'] ?? []);
        }
    }

    /**
     * Process type normalization map and aliases.
     */
    private function processNormalizationStaticForContext(ContainerBuilder $container, string $tag, array $map, array $aliases): void
    {
        $types = [];

        // Current expected map array uses class names as keys
        // and aliases as values. But older projects done it reverse.
        // Detect this in the first row, and treat it as reversed for
        // all other then for backward compatibility.
        $isReversed = false;

        foreach ($map as $type => $key) {
            // Inversion detection.
            $a = $this->normalizeType($type, $key);
            $b = $this->normalizeType($key, $type);
            if (\class_exists($b) && !\class_exists($a)) {
                $isReversed = true;
            }
        }

        if ($isReversed) {
            \trigger_error(\sprintf("Warning, the 'normalization.static.%s.map' is inversed, keys should be class names and values should be names.", $tag), E_USER_DEPRECATED);
            $map = \array_flip($map);
        }

        foreach ($map as $type => $key) {
            $type = $this->normalizeType($type, $key);

            if ('string' !== $type && 'array' !== $type && 'null' !== $type && !\class_exists($type)) {
                throw new InvalidArgumentException(\sprintf(
                    "normalization.map: key '%s': class '%s' does not exist",
                    $key, $type
                ));
            }
            if ($existing = ($types[$type] ?? null)) {
                throw new InvalidArgumentException(\sprintf(
                    "normalization.map: key '%s': class '%s' previously defined at key '%s'",
                    $key, $type, $existing
                ));
            }
            // Value is normalized, fix incomming array.
            $map[$key] = $type;
            $types[$type] = $key;
        }

        foreach ($aliases as $alias => $type) {
            $type = $this->normalizeType($type, $key);
            // Alias toward another alias, or alias toward an PHP native type?
            if (!isset($map[$alias]) && !\in_array($type, $map)) {
                if ($existing = ($types[$type] ?? null)) {
                    throw new InvalidArgumentException(\sprintf(
                        "normalization.alias: key '%s': normalized name or type '%s' is not defined in normalization.map",
                        $alias, $type, $existing
                    ));
                }
            }
            $aliases[$alias] = $type;
        }

        $container->getDefinition('normalization.name_map')->addMethodCall('setStaticNameMap', [$map, $aliases, $tag]);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new NormalizationConfiguration();
    }
}
