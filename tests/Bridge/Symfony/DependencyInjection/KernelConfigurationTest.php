<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalization\Tests\Bridge\Symfony\DependencyInjection;

use MakinaCorpus\Normalization\Bridge\Symfony\DependencyInjection\NormalizationExtension;
use MakinaCorpus\Normalization\NameMap\PassthroughNameMappingStrategy;
use MakinaCorpus\Normalization\Tests\Mock\MockMessage1;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

final class KernelConfigurationTest extends TestCase
{
    private function getContainer()
    {
        // Code inspired by the SncRedisBundle, all credits to its authors.
        $container = new ContainerBuilder(new ParameterBag([
            'kernel.debug'=> false,
            'kernel.bundles' => [],
            'kernel.cache_dir' => \sys_get_temp_dir(),
            'kernel.environment' => 'test',
            'kernel.root_dir' => \dirname(__DIR__),
        ]));

        return $container;
    }

    private function getMinimalConfig(): array
    {
        return [
            'default_strategy' => [
                'app_name' => 'SomeVendor\\SomeApp',
                'class_prefix' => 'VendorApp',
            ],
            'strategy' => [
                'foo' => PassthroughNameMappingStrategy::class,
            ],
            'static' => [
                'some_tag' => [
                    'map' => [
                        MockMessage1::class => 'mock.message',
                    ],
                    'aliases' => [
                        'MockMessage' => MockMessage1::class,
                    ],
                ],
            ],
        ];
    }

    /**
     * Test default config for resulting tagged services
     */
    public function testTaggedServicesConfigLoad()
    {
        $extension = new NormalizationExtension();
        $config = $this->getMinimalConfig();
        $extension->load([$config], $container = $this->getContainer());

        // self::assertTrue($container->hasAlias(PreferencesRepository::class));
        // self::assertTrue($container->hasAlias('preferences.repository'));

        // self::assertTrue($container->hasDefinition('preferences.repository.goat_query'));
        // self::assertTrue($container->hasDefinition('preferences.env_var_processor'));

        $container->compile();

        // If we explicitely expect no assertion, coverage is disabled.
        self::assertTrue(true);
    }
}
