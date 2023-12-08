<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalization\NameMap;

use MakinaCorpus\Normalization\NameMap;
use MakinaCorpus\Normalization\NameMapList;

/**
 * Array based static name map.
 */
class ArrayNameMap implements NameMap, NameMapList
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

    #[\Override]
    public function listTags(): array
    {
        $ret = [NameMap::TAG_DEFAULT];
        foreach ($this->logicalToPhp as $phpTypes) {
            $ret += \array_keys($phpTypes);
        }
        foreach ($this->phpToLogical as $aliases) {
            $ret += \array_keys($aliases);
        }
        return \array_unique($ret);
    }

    #[\Override]
    public function listAliases(string $tag = NameMap::TAG_DEFAULT): array
    {
        $ret = [];
        foreach ($this->logicalToPhp as $alias => $phpTypes) {
            foreach ($phpTypes as $localTag => $phpType) {
                if ($localTag === $tag) {
                    $ret[$alias] = $phpType;
                }
            }
        }
        return $ret;
    }

    #[\Override]
    public function listPhpTypes(string $tag = NameMap::TAG_DEFAULT): array
    {
        $ret = [];
        foreach ($this->phpToLogical as $phpType => $aliases) {
            foreach ($aliases as $localTag => $alias) {
                if ($localTag === $tag) {
                    $ret[$phpType] = $alias;
                }
            }
        }
        return $ret;
    }

    #[\Override]
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

    #[\Override]
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
