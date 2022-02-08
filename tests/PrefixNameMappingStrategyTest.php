<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalization\Tests;

use MakinaCorpus\Normalization\NameMap\PrefixNameMappingStrategy;
use PHPUnit\Framework\TestCase;

class PrefixNameMappingStrategyTest extends TestCase
{
    public function testPrefixMatchFromPhpType(): void
    {
        $strategy = new PrefixNameMappingStrategy('Grumf', '\\Foo\\Bar');

        self::assertSame('Grumf.Fizz.Buzz', $strategy->fromPhpType('Foo\\Bar\\Fizz\\Buzz'));
    }

    public function testPrefixMatchFromPhpTypeShort(): void
    {
        $strategy = new PrefixNameMappingStrategy('Grumf', '\\Foo\\Bar');

        self::assertSame('Grumf.Fizz', $strategy->fromPhpType('Foo\\Bar\\Fizz'));
    }

    public function testPrefixMatchFromPhpTypeLong(): void
    {
        $strategy = new PrefixNameMappingStrategy('Grumf', '\\Foo\\Bar');

        self::assertSame('Grumf.Fizz.Buzz.Halt.Catch.Fire', $strategy->fromPhpType('Foo\\Bar\\Fizz\\Buzz\\Halt\\Catch\\Fire'));
    }

    public function testPrefixMatchFromPhpTypeTooShort(): void
    {
        $strategy = new PrefixNameMappingStrategy('Grumf', '\\Foo\\Bar');

        self::assertSame('Foo\\Bar', $strategy->fromPhpType('Foo\\Bar'));
    }

    public function testPrefixMatchToPhpType(): void
    {
        $strategy = new PrefixNameMappingStrategy('Grumf', '\\Foo\\Bar');

        self::assertSame('Foo\\Bar\\Fizz\\Buzz', $strategy->toPhpType('Grumf.Fizz.Buzz'));
    }

    public function testPrefixMatchToPhpTypeShort(): void
    {
        $strategy = new PrefixNameMappingStrategy('Grumf', '\\Foo\\Bar');

        self::assertSame('Foo\\Bar\\Fizz', $strategy->toPhpType('Grumf.Fizz'));
    }

    public function testPrefixMatchToPhpTypeLong(): void
    {
        $strategy = new PrefixNameMappingStrategy('Grumf', '\\Foo\\Bar');

        self::assertSame('Foo\\Bar\\Fizz\\Buzz\\Halt\\Catch\\Fire', $strategy->toPhpType('Grumf.Fizz.Buzz.Halt.Catch.Fire'));
    }

    public function testPrefixMatchToPhpTypeTooShort(): void
    {
        $strategy = new PrefixNameMappingStrategy('Grumf', '\\Foo\\Bar');

        self::assertSame('Grumf', $strategy->toPhpType('Grumf'));
    }

    public function testPrefixDoesNotMatchFromPhpType(): void
    {
        $strategy = new PrefixNameMappingStrategy('Grumf', '\\Foo\\Bar');

        // No conversion.
        self::assertSame('Bar\\Foo\\Bla', $strategy->fromPhpType('Bar\\Foo\\Bla'));
    }

    public function testPrefixDoesNotMatchToPhpType(): void
    {
        $strategy = new PrefixNameMappingStrategy('Grumf', '\\Foo\\Bar');

        // No conversion.
        self::assertSame('Bar.Foo.Bla', $strategy->toPhpType('Bar.Foo.Bla'));
    }
}
