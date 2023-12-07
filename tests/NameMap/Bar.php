<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalization\Tests\NameMap;

use MakinaCorpus\Normalization\Alias;

#[Alias(name: "bar_default_alias")]
#[Alias(name: "bar_other_alias", tag: "other")]
#[Alias(name: "bar_another_alias", tag: "another")]
class Bar
{
}
