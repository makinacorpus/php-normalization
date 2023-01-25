<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalization\Tests\Builder;

use MakinaCorpus\Normalization\Builder\NameMapBuilder;
use PHPUnit\Framework\TestCase;

final class NameMapBuilderTest extends TestCase
{
    public function testAttributePriority(): void
    {
        $builder = new NameMapBuilder();
        $builder->addFromClass(ClassWithAttributes::class);
        $builder->addFromClass(ClassWithAttributesThatConflicts::class);

        self::assertSame(
            [
                ClassWithAttributes::class => [
                    'other' => 'first_tag_first',
                    'default' => 'first',
                    'event' => 'tag_first',
                    'deprecated_tag' => 'deprecated_no_alt',
                ],
                ClassWithAttributesThatConflicts::class => [
                    'event' => 'tag_first',
                    'default' => 'class_that_conflicts'
                ],
            ],
            $builder->getPhpToLogicalNameMap()
        );

        self::assertSame(
            [
                'second' => [
                    'default' => ClassWithAttributes::class,
                ],
                'first' => [
                    'default' => ClassWithAttributes::class,
                ],
                'deprecated' => [
                    'default' => ClassWithAttributes::class,
                ],
                'deprecated_no_alt' => [
                    'deprecated_tag' => ClassWithAttributes::class,
                ],
                'tag_second' => [
                    'event' => ClassWithAttributes::class,
                ],
                'tag_first' => [
                    'event' => ClassWithAttributesThatConflicts::class,
                ],
                'first_tag_first' => [
                    'other' => ClassWithAttributes::class,
                ],
                'first_tag_second' => [
                    'other' => ClassWithAttributes::class,
                ],
                'class_that_conflicts' => [
                    'default' => ClassWithAttributesThatConflicts::class,
                ],
            ],
            $builder->getLogicalToPhpNameMap()
        );
    }

    public function testLegacyConfig(): void
    {
        $builder = new NameMapBuilder();
        $builder->addFromLegacyConfig([
            'default' => [
                'map' => [
                    ClassWithAttributes::class => 'first',
                ],
            ],
            'event' => [
                'map' => [
                    ClassWithAttributes::class => 'second',
                    ClassWithAttributesThatConflicts::class => 'first',
                ],
                'aliases' => [
                    'third' => ClassWithAttributes::class,
                ],
            ],
            'other' => [
                'map' => [
                    ClassWithAttributesThatConflicts::class => 'conflict',
                ],
                'aliases' => [
                    'conflict' => ClassWithAttributes::class,
                ],
            ],
        ]);

        self::assertSame(
            [
                ClassWithAttributes::class => [
                    'default' => 'first',
                    'event' => 'second',
                ],
                ClassWithAttributesThatConflicts::class => [
                    'event' => 'first',
                    'other' => 'conflict',
                ],
            ],
            $builder->getPhpToLogicalNameMap()
        );

        self::assertSame(
            [
                'first' => [
                    'default' => ClassWithAttributes::class,
                    'event' => ClassWithAttributesThatConflicts::class,
                ],
                'second' => [
                    'event' => ClassWithAttributes::class,
                ],
                'third' => [
                    'event' => ClassWithAttributes::class,
                ],
                'conflict' => [
                    'other' => ClassWithAttributesThatConflicts::class,
                ],
            ],
            $builder->getLogicalToPhpNameMap()
        );
    }

    public function testLegacyConfigReversed(): void
    {
        $builder = new NameMapBuilder();
        $builder->addFromLegacyConfig([
            'default' => [
                'map' => [
                    'first' => ClassWithAttributes::class,
                ],
            ],
            'event' => [
                'map' => [
                    'second' => ClassWithAttributes::class,
                    'first' => ClassWithAttributesThatConflicts::class,
                ],
                'aliases' => [
                    'third' => ClassWithAttributes::class,
                ],
            ],
            'other' => [
                'map' => [
                    'conflict' => ClassWithAttributesThatConflicts::class,
                ],
                'aliases' => [
                    'conflict' => ClassWithAttributes::class,
                ],
            ],
        ]);

        self::assertSame(
            [
                ClassWithAttributes::class => [
                    'default' => 'first',
                    'event' => 'second',
                ],
                ClassWithAttributesThatConflicts::class => [
                    'event' => 'first',
                    'other' => 'conflict',
                ],
            ],
            $builder->getPhpToLogicalNameMap()
        );

        self::assertSame(
            [
                'first' => [
                    'default' => ClassWithAttributes::class,
                    'event' => ClassWithAttributesThatConflicts::class,
                ],
                'second' => [
                    'event' => ClassWithAttributes::class,
                ],
                'third' => [
                    'event' => ClassWithAttributes::class,
                ],
                'conflict' => [
                    'other' => ClassWithAttributesThatConflicts::class,
                ],
            ],
            $builder->getLogicalToPhpNameMap()
        );
    }
}
