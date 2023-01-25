<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalization\NameMap;

use MakinaCorpus\Normalization\NameMap;

/**
 * Array based static name map.
 */
class ArrayNameMap implements NameMap
{
    /** @var array<string,array<string,string>> */
    private array $logicalToPhp = [];
    /** @var array<string,array<string,string>> */
    private array $phpToLogical = [];

    /**
     * @param array<name,array<tag,phpType> $logicalToPhp
     *   Tags must be order by priority.
     * @param array<phpType,array<tag,name> $phpToLogical
     *   Tags must be order by priority.
     */
    public function __construct(array $logicalToPhp, array $phpToLogical)
    {
        $this->logicalToPhp = $logicalToPhp;
        $this->phpToLogical = $phpToLogical;
    }

    /**
     * {@inheritdoc}
     */
    public function toPhpType(string $name, ?string $tag = null): string
    {
        if ($values = ($this->logicalToPhp[$name] ?? null)) {
            if (null === $tag) {
                foreach ($values as $tag => $value) {
                    return $value;
                }
            } else if ($tag === self::TAG_DEFAULT) {
                return $values[$tag] ?? $name;
            } else {
                return $values[$tag] ?? $values[self::TAG_DEFAULT] ?? $name;
            }
        }
        return $name;
    }

    /**
     * {@inheritdoc}
     */
    public function fromPhpType(string $phpType, ?string $tag = null): string
    {
        if ($values = ($this->phpToLogical[$phpType] ?? null)) {
            if (null === $tag) {
                foreach ($values as $tag => $value) {
                    return $value;
                }
            } else if ($tag === self::TAG_DEFAULT) {
                return $values[$tag] ?? $phpType;
            } else {
                return $values[$tag] ?? $values[self::TAG_DEFAULT] ?? $phpType;
            }
        }
        return $phpType;
    }
}
