<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalization\Tests\Builder;

use MakinaCorpus\Normalization\Alias;

#[Alias(name: "tag_first", tag: "event", priority: 100)]
#[Alias(name: "class_that_conflicts")]
class ClassWithAttributesThatConflicts
{
}
