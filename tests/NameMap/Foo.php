<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalization\Tests\NameMap;

#[\MakinaCorpus\Normalization\DomainAlias(name: "foo_other_alias", tag: "other")]
#[\MakinaCorpus\Normalization\DomainAlias(name: "foo_another_alias", tag: "another")]
class Foo
{
}
