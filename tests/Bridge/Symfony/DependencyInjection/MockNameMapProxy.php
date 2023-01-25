<?php

declare (strict_types=1);

namespace MakinaCorpus\Normalization\Tests\Bridge\Symfony\DependencyInjection;

use MakinaCorpus\Normalization\NameMap;

class MockNameMapProxy
{
    private NameMap $nameMap;

    public function __construct(NameMap $nameMap)
    {
        $this->nameMap = $nameMap;
    }

    public function getNameMap(): NameMap
    {
        return $this->nameMap;
    }
}
