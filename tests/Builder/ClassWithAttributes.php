<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalization\Tests\Builder;

use MakinaCorpus\Normalization\Alias;

#[Alias(name: "second", priority: 0)]
#[Alias(name: "first", priority: 12)]
#[Alias(name: "deprecated", priority: 1000, deprecated: true)]
#[Alias(name: "deprecated_no_alt", tag: "deprecated_tag", priority: 10000, deprecated: true)]
#[Alias(name: "tag_second", tag: "event", priority: -100)]
#[Alias(name: "tag_first", tag: "event", priority: 0)]
#[Alias(name: "first_tag_first", tag: "other", priority: 1000)]
#[Alias(name: "first_tag_second", tag: "other", priority: -100)]
class ClassWithAttributes
{
}
