<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalization\Builder;

/**
 * Optimized name map builder.
 */
class NameMapBuilderItem
{
    public string $value;
    public int $priority;
    public bool $deprecated;

    public function __construct(
        string $value,
        int $priority,
        bool $deprecated
    ) {
        $this->value = $value;
        $this->priority = $priority;
        $this->deprecated = $deprecated;
    }
}
