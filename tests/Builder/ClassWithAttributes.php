<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalization\Tests\Builder;

#[\MakinaCorpus\Normalization\DomainAlias(name: "second", priority: 0)]
#[\MakinaCorpus\Normalization\DomainAlias(name: "first", priority: 12)]
#[\MakinaCorpus\Normalization\DomainAlias(name: "deprecated", priority: 1000, deprecated: true)]
#[\MakinaCorpus\Normalization\DomainAlias(name: "deprecated_no_alt", tag: "deprecated_tag", priority: 10000, deprecated: true)]
#[\MakinaCorpus\Normalization\DomainAlias(name: "tag_second", tag: "event", priority: -100)]
#[\MakinaCorpus\Normalization\DomainAlias(name: "tag_first", tag: "event", priority: 0)]
#[\MakinaCorpus\Normalization\DomainAlias(name: "first_tag_first", tag: "other", priority: 1000)]
#[\MakinaCorpus\Normalization\DomainAlias(name: "first_tag_second", tag: "other", priority: -100)]
class ClassWithAttributes
{
}
