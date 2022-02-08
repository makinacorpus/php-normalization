<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalization\Bridge\Symfony;

use MakinaCorpus\Normalization\Bridge\Symfony\DependencyInjection\NormalizationExtension;
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
    }

    /**
     * {@inheritdoc}
     */
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new NormalizationExtension();
    }
}
