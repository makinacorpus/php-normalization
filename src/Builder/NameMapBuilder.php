<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalization\Builder;

use MakinaCorpus\Normalization\DomainAlias;

/**
 * Optimized name map builder.
 *
 * This will be used during cache building time, for Symfony for example
 * during container rebuild. All useless declarations will be stripped out
 * from result, priorities and deprecation status will be already taken
 * care of, and resulting array values will be ready to be dumped.
 */
class NameMapBuilder
{
    /** @var NameMapBuilderItem[][] */
    private $logicalToPhp = [];
    /** @var NameMapBuilderItem[][] */
    private $phpToLogical = [];

    /**
     * @param array<name,array<tag,phpType>
     *   Tags ordered by priority.
     */
    public function getLogicalToPhpNameMap(): array
    {
        return $this->createSortedArrayOf($this->logicalToPhp);
    }

    /**
     * @param array<phpType,array<tag,name>
     *   Tags ordered by priority.
     */
    public function getPhpToLogicalNameMap(): array
    {
        return $this->createSortedArrayOf($this->phpToLogical);
    }

    /**
     * Add a single class alias entry.
     */
    public function add(string $phpType, string $name, string $tag, int $priority, bool $deprecated): void
    {
        $existing = $this->phpToLogical[$phpType][$tag] ?? null;
        if (!$existing || (
            // Previous was deprecated, we take this one.
            (($existing->deprecated && !$deprecated) || $existing->deprecated === $deprecated) &&
            // Or new item priority is higher than the previous one.
            $existing->priority < $priority
        )) {
            $this->phpToLogical[$phpType][$tag] = new NameMapBuilderItem($name, $priority, $deprecated);
        }

        $existing = $this->logicalToPhp[$name][$tag] ?? null;
        if (!$existing ||
            // Previous was deprecated, we take this one.
            (($existing->deprecated && !$deprecated) || $existing->deprecated === $deprecated) &&
            // Or new item priority is higher than the previous one.
            $existing->priority < $priority
        ) {
            $this->logicalToPhp[$name][$tag] = new NameMapBuilderItem($phpType, $priority, $deprecated);
        }
    }

    /**
     * Add a single class alias entry using an attribute.
     */
    public function addAttribute(string $phpType, DomainAlias $attribute): void
    {
        $this->add($phpType, $attribute->getName(), $attribute->getTag(), $attribute->getPriority(), $attribute->isDeprecated());
    }

    /**
     * Add all entries from class using its attribute information.
     */
    public function addFromClass(string $phpType): void
    {
        if (!\class_exists($phpType) && !\interface_exists($phpType)) {
            throw new \InvalidArgumentException(\sprintf("%s: class or interface does not exist", $phpType));
        }

        $refClass = new \ReflectionClass($phpType);
        foreach ($refClass->getAttributes(DomainAlias::class) as $refAttr) {
            $this->addAttribute($phpType, $refAttr->newInstance());
        }
    }

    /**
     * From legacy configuration array.
     *
     * Array structure is the following:
     *
     *   // Top level keys are tag names.
     *   "TAG" => [
     *     // Static map of class to alias, each class can have only a single
     *     // alias, it will be converted to be the one with the highest
     *     // priority.
     *     "map" => [
     *       "CLASS_NAME" => "ALIAS",
     *       ...
     *     ],
     *     // Previous aliases, keys are aliases, values are class names.
     *     // Each element in this array will be marked as deprecated, and
     *     // thus having a lowest priority.
     *     "aliaes" => [
     *       "PREVIOUS_CLASS_NAME_OR_ALIAS" => "CLASS_NAME",
     *     ],
     *     ...
     *   ],
     *
     * This legacy model can have the "CLASS_NAME" => "ALIAS" section reversed
     * for backward compatibility purpose, case in which first element will have
     * the highest priority.
     */
    public function addFromLegacyConfig(array $config): void
    {
        $priorityMap = [];

        foreach ($config as $tag => $data) {
            $map = $data['map'] ?? [];
            if ($map) {
                // Current expected map array uses class names as keys
                // and aliases as values. But older projects done it reverse.
                // Detect this in the first row, and treat it as reversed for
                // all other then for backward compatibility.
                $isReversed = false;

                foreach ($map as $type => $name) {
                    // Inversion detection.
                    $a = $this->normalizeType($type, $name, $tag);
                    $b = $this->normalizeType($name, $type, $tag);
                    if ((\class_exists($b) || \interface_exists($b)) && !(\class_exists($a) || \interface_exists($a))) {
                        $isReversed = true;
                        break;
                    }
                }

                if ($isReversed) {
                    \trigger_error(\sprintf("Warning, the 'normalization.static.%s.map' is inversed, keys should be class names and values should be names.", $tag), E_USER_DEPRECATED);
                    $map = \array_flip($map);
                }

                foreach ($map as $type => $name) {
                    $phpType = $this->normalizeType($type, $name, $tag);

                    if ('string' !== $phpType && 'array' !== $phpType && 'null' !== $phpType && !\class_exists($phpType)) {
                        throw new \InvalidArgumentException(\sprintf(
                            "normalization.map.%s: key '%s': class '%s' does not exist",
                            $tag, $name, $type
                        ));
                    }

                    $priority = $priorityMap[$phpType][$tag] ?? 0;
                    $priorityMap[$phpType][$tag] = $priority - 1;

                    $this->add($phpType, $name, $tag, $priority, false);
                }
            }

            $aliases = $data['aliases'] ?? [];
            if ($aliases) {
                foreach ($aliases as $name => $type) {
                    $phpType = $this->normalizeType($type, $name, $tag);

                    $priority = $priorityMap[$phpType][$tag] ?? 0;
                    $priorityMap[$phpType][$tag] = $priority - 1;

                    $this->add($phpType, $name, $tag, $priority, true);
                }
            }
        }
    }

    /**
     * Normalize type name, do not check for type existence?
     *
     * @codeCoverageIgnore
     */
    private function normalizeType(string $type, string $key, string $tag): string
    {
        if (!\is_string($type)) {
            throw new \InvalidArgumentException(\sprintf("normalization.map.%s: key '%s': value must be a string", $tag, $key));
        }
        if (\ctype_digit($key)) {
            throw new \InvalidArgumentException(\sprintf("normalization.map.%s: key '%s': cannot be numeric", $tag, $key));
        }
        // Normalize to FQDN
        return \ltrim(\trim($type), '\\');
    }

    /**
     * Create sorted by priority and deprecation array of given name builder
     * item list. Deprecated items are always last, otherwise priority is used
     * for sorting (in natural reverse order, higher first).
     */
    private function createSortedArrayOf(array $map)
    {
        $ret = [];
        foreach ($map as $name => $tags) {
            \uasort($tags, function (NameMapBuilderItem $a, NameMapBuilderItem $b) {
                if ($a->deprecated && !$b->deprecated) {
                    return 1;
                }
                if ($b->deprecated && !$a->deprecated) {
                    return -1;
                }
                return $b->priority - $a->priority;
            });
            foreach ($tags as $tag => $item) {
                \assert($item instanceof NameMapBuilderItem);
                $ret[$name][$tag] = $item->value;
            }
        }
        return $ret;
    }
}
