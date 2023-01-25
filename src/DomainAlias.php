<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalization;

/**
 * Defines one logical or business name for the target class.
 *
 * This name will be injected into the runtime NameMap instance, along with
 * the given tag.
 *
 * If you provide no tag, "default" will be used instead.
 *
 * You can add this attribute as many time as you wish, each time with a
 * different or same tag, allowing you to give either:
 *
 *  - one or more aliases for a single tag,
 *  - one more aliases for different tags.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class DomainAlias
{
    private string $name;
    private string $tag;
    private int $priority;
    private bool $deprecated;

    /**
     * @param string $name
     *   Logical or business name of the target class.
     * @param null|string $tag
     *   Associated tag with this name. If none provided, it will be associated
     *   with the "default" tag.
     * @param null|int $priority
     *   Priority, highest number will have the highest priority. If none
     *   provided defaults to 0.
     * @param bool $deprecated
     *   If set to true, this name will be considered as obsolete, and will
     *   never be selected by default if alternatives exist. It also will raise
     *   deprecation notices upon usage when requested throught the NameMap.
     */
    public function __construct(string $name, ?string $tag = null, ?int $priority = null, bool $deprecated = false)
    {
        $this->name = $name;
        $this->tag = $tag ?? NameMap::TAG_DEFAULT;
        $this->priority = $priority ?? 0;
        $this->deprecated = $deprecated;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTag(): string
    {
        return $this->tag;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function isDeprecated(): bool
    {
        return $this->deprecated;
    }
}
