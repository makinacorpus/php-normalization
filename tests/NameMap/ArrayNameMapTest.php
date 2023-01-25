<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalization\Tests\NameMap;

use MakinaCorpus\Normalization\Builder\NameMapBuilder;
use MakinaCorpus\Normalization\NameMap\ArrayNameMap;
use PHPUnit\Framework\TestCase;

final class ArrayNameMapTest extends TestCase
{
    private ?ArrayNameMap $map = null;

    protected function setUp(): void
    {
        $builder = new NameMapBuilder();
        $builder->addFromClass(Bar::class);
        $builder->addFromClass(Foo::class);

        $this->map = new ArrayNameMap(
            $builder->getLogicalToPhpNameMap(),
            $builder->getPhpToLogicalNameMap()
        );
    }

    public function testToPhpTypeWithTag(): void
    {
        self::assertSame(Bar::class, $this->map->toPhpType('bar_default_alias', 'default'));
        self::assertSame(Bar::class, $this->map->toPhpType('bar_other_alias', 'other'));
        self::assertSame(Bar::class, $this->map->toPhpType('bar_another_alias', 'another'));

        self::assertSame(Foo::class, $this->map->toPhpType('foo_other_alias', 'other'));
        self::assertSame(Foo::class, $this->map->toPhpType('foo_another_alias', 'another'));
    }

    public function testToPhpTypeWithoutTagGetDefault(): void
    {
        self::assertSame(Bar::class, $this->map->toPhpType('bar_default_alias'));
        self::assertSame(Bar::class, $this->map->toPhpType('bar_other_alias'));
        self::assertSame(Bar::class, $this->map->toPhpType('bar_another_alias'));
    }

    public function testToPhpTypeWithoutTagGetFirst(): void
    {
        self::assertSame(Foo::class, $this->map->toPhpType('foo_other_alias'));
        self::assertSame(Foo::class, $this->map->toPhpType('foo_another_alias'));
    }

    public function testFromPhpTypeWithTag(): void
    {
        self::assertSame('bar_default_alias', $this->map->fromPhpType(Bar::class, 'default'));
        self::assertSame('bar_other_alias', $this->map->fromPhpType(Bar::class, 'other'));
        self::assertSame('bar_another_alias', $this->map->fromPhpType(Bar::class, 'another'));

        self::assertSame('foo_other_alias', $this->map->fromPhpType(Foo::class, 'other'));
        self::assertSame('foo_another_alias', $this->map->fromPhpType(Foo::class, 'another'));
    }

    public function testFromPhpTypeWithoutTagGetDefault(): void
    {
        self::assertSame('bar_default_alias', $this->map->fromPhpType(Bar::class));
    }

    public function testFromPhpTypeWithoutTagGetFirst(): void
    {
        self::assertSame('foo_other_alias', $this->map->fromPhpType(Foo::class));
    }
}
