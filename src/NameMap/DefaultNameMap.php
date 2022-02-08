<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalization\NameMap;

use MakinaCorpus\Normalization\NameMap;
use MakinaCorpus\Normalization\NameMappingStrategy;

/**
 * Default name mapper: use a strategy per context.
 *
 * Brings aliasing as well.
 */
class DefaultNameMap implements NameMap
{
    private NameMappingStrategy $defaultStrategy;
    /** @var array<string,NameMappingStrategy> */
    private array $strategies = [];

    /** @var array<string,array<string,string>> */
    private array $map = [];
    /** @var array<string,array<string,string>> */
    private array $aliases = [];

    public function __construct(?NameMappingStrategy $defaultStrategy = null)
    {
        $this->defaultStrategy = $defaultStrategy ?? new PassthroughNameMappingStrategy();
    }

    /**
     * {@inheritdoc}
     */
    public function toPhpType(string $name, string $tag = self::TAG_DEFAULT): string
    {
        if (isset($this->map[$tag][$name])) {
            return $name;
        }

        return $this->aliases[$tag][$name] ?? $this->getNameMappingStrategy($tag)->toPhpType($name);
    }

    /**
     * {@inheritdoc}
     */
    public function fromPhpType(string $phpType, string $tag = self::TAG_DEFAULT): string
    {
        if (isset($this->aliases[$tag][$phpType])) {
            return $phpType;
        }

        return $this->map[$tag][$phpType] ?? $this->getNameMappingStrategy($tag)->fromPhpType($phpType);
    }

    /**
     * Set static name map for a given tag.
     *
     * @param array<string,string> $map
     *   Keys are PHP type names, values are aliases. Converts PHP type names
     *   to their actual names.
     * @param array<string,string> $aliases
     *   Keys are aliases, values are PHP type names. Converts possibly obsolete
     *   aliases to the real PHP type name. 
     */
    public function setStaticNameMap(array $map, array $aliases = [], string $tag = self::TAG_DEFAULT): void
    {
        $this->map[$tag] = $map;
        $this->aliases[$tag] = \array_flip($map) + $aliases;
    }

    /**
     * 
     * @param \MakinaCorpus\Normalization\NameMappingStrategy $strategy
     * @param string $tag
     */
    public function setNameMappingStrategy(NameMappingStrategy $strategy, string $tag = self::TAG_DEFAULT): void
    {
        if (self::TAG_DEFAULT === $tag) {
            $this->defaultStrategy = $strategy;
        } else {
            $this->strategies[$tag] = $strategy;
        }
    }

    /**
     * Get name mapping strategy.
     */
    private function getNameMappingStrategy(string $tag): NameMappingStrategy
    {
        if (self::TAG_DEFAULT === $tag) {
            return $this->defaultStrategy;
        } else {
            return $this->strategies[$tag] ?? $this->defaultStrategy;
        }
    }
}
