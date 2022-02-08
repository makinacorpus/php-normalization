<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalization\NameMap;

use MakinaCorpus\Normalization\NameMap;

interface NameMapAware
{
    /**
     * Get or create empty namespace map.
     *
     * @internal
     */
    public function getNameMap(): NameMap;

    /**
     * {@inheritdoc}
     */
    public function setNameMap(NameMap $nameMap): void;
}
