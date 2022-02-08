<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalization\NameMap;

use MakinaCorpus\Normalization\NameMappingStrategy;

class PassthroughNameMappingStrategy implements NameMappingStrategy
{
    /**
     * {@inheritdoc}
     */
    public function toPhpType(string $name): string
    {
        return $name;
    }

    /**
     * {@inheritdoc}
     */
    public function fromPhpType(string $phpType): string
    {
        return $phpType;
    }
}
