<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalization\Tests\NameMap;

use MakinaCorpus\Normalization\Alias;

#[Alias(name: "foo_other_alias", tag: "other")]
#[Alias(name: "foo_another_alias", tag: "another")]
class Foo
{
}
