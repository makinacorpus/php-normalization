<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalization\Tests\Builder;

#[\MakinaCorpus\Normalization\DomainAlias(name: "tag_first", tag: "event", priority: 100)]
#[\MakinaCorpus\Normalization\DomainAlias(name: "class_that_conflicts")]
class ClassWithAttributesThatConflicts
{
}
