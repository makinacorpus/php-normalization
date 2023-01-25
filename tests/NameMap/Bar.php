<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalization\Tests\NameMap;

#[\MakinaCorpus\Normalization\DomainAlias(name: "bar_default_alias")]
#[\MakinaCorpus\Normalization\DomainAlias(name: "bar_other_alias", tag: "other")]
#[\MakinaCorpus\Normalization\DomainAlias(name: "bar_another_alias", tag: "another")]
class Bar
{
}
