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
    private ?NameMap $staticNameMap = null;
    private NameMappingStrategy $defaultStrategy;
    /** @var array<string,NameMappingStrategy> */
    private array $strategies = [];

    public function __construct(?NameMappingStrategy $defaultStrategy = null, ?NameMap $staticNameMap = null)
    {
        $this->defaultStrategy = $defaultStrategy ?? new PassthroughNameMappingStrategy();
        $this->staticNameMap = $staticNameMap;
    }

    /**
     * {@inheritdoc}
     */
    public function toPhpType(string $name, ?string $tag = null): string
    {
        if ($this->staticNameMap && ($ret = $this->staticNameMap->toPhpType($name)) && $ret !== $name) {
            return $ret;
        }
        return $this->getNameMappingStrategy($tag)->toPhpType($name);
    }

    /**
     * {@inheritdoc}
     */
    public function fromPhpType(string $phpType, ?string $tag = null): string
    {
        if ($this->staticNameMap && ($ret = $this->staticNameMap->fromPhpType($phpType, $tag)) && $ret !== $phpType) {
            return $ret;
        }
        return $this->getNameMappingStrategy($tag)->fromPhpType($phpType);
    }

    /**
     * Set static name map.
     */
    public function setStaticNameMap(NameMap $staticNameMap): void
    {
        $this->staticNameMap = $staticNameMap;
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
