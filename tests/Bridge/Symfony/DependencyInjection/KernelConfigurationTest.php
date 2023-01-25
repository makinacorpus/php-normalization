<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalization\Tests\Bridge\Symfony\DependencyInjection;

use MakinaCorpus\Normalization\NameMap;
use MakinaCorpus\Normalization\Bridge\Symfony\DependencyInjection\NormalizationExtension;
use MakinaCorpus\Normalization\Bridge\Symfony\DependencyInjection\Compiler\RegisterStaticNameMapPass;
use MakinaCorpus\Normalization\Tests\Mock\MockMessage1;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

final class KernelConfigurationTest extends TestCase
{
    private function getContainer(): ContainerBuilder
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
                'foo' => 'normalization.name_map.strategy.prefix',
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

        $definition = new Definition();
        $definition->setClass(MockRegisteredWithAttributeService::class);
        $definition->addTag('normalization.aliased');
        $definition->setPublic(true);
        $container->setDefinition(MockRegisteredWithAttributeService::class, $definition);

        $definition = new Definition();
        $definition->setClass(MockNameMapProxy::class);
        $definition->setArguments([new Reference(NameMap::class)]);
        $definition->setPublic(true);
        $container->setDefinition(MockNameMapProxy::class, $definition);

        $container->addCompilerPass(new RegisterStaticNameMapPass());
        $container->compile();

        $nameMapProxy = $container->get(MockNameMapProxy::class);
        \assert($nameMapProxy instanceof MockNameMapProxy);
        self::assertInstanceOf(NameMap::class, $nameMapProxy->getNameMap());
        self::assertSame('mock.message', $nameMapProxy->getNameMap()->fromPhpType(MockMessage1::class));
        self::assertSame('some_mock_alias', $nameMapProxy->getNameMap()->fromPhpType(MockRegisteredWithAttributeService::class));

        // If we explicitely expect no assertion, coverage is disabled.
        self::assertTrue(true);
    }
}
