<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalization\Tests\NameMap;

use MakinaCorpus\Normalization\NameMap;
use MakinaCorpus\Normalization\NameMappingStrategy;
use MakinaCorpus\Normalization\NameMap\ArrayNameMap;
use MakinaCorpus\Normalization\NameMap\DefaultNameMap;
use MakinaCorpus\Normalization\Tests\Mock\MockMessage1;
use MakinaCorpus\Normalization\Tests\Mock\MockMessage2;
use MakinaCorpus\Normalization\Tests\Mock\MockMessage3;
use PHPUnit\Framework\TestCase;

final class DefaultNameMapTest extends TestCase
{
    private ?NameMap $map = null;

    protected function setUp(): void
    {
        $this->map = new DefaultNameMap();
        $this->map->setStaticNameMap(
            new ArrayNameMap(
                [
                    'mock_2' => [NameMap::TAG_COMMAND => MockMessage2::class],
                    'mock_2_2' => [NameMap::TAG_COMMAND => MockMessage2::class],
                    'mock_3' => [NameMap::TAG_COMMAND => 'mock_message_3'],
                    'mock_message_2' => [NameMap::TAG_COMMAND => MockMessage2::class],
                    'mock_message_3' => [NameMap::TAG_COMMAND => MockMessage3::class],
                    'non_existing_class' => [NameMap::TAG_COMMAND => 'NonExistingClass'],
                ],
                [
                    'NonExistingClass' => [NameMap::TAG_COMMAND => 'non_existing_class'],
                    MockMessage2::class => [NameMap::TAG_COMMAND => 'mock_message_2'],
                    MockMessage3::class => [NameMap::TAG_COMMAND => 'mock_message_3'],
                ],
            ),
        );

        $this->map->setNameMappingStrategy(
            new class () implements NameMappingStrategy
            {
                public function toPhpType(string $name): string
                {
                    if ('%%' !== \substr($name, 0, 2)) {
                        return $name;
                    }
                    return \substr($name, 2);
                }

                public function fromPhpType(string $phpType): string
                {
                    if ('%%' === \substr($phpType, 0, 2)) {
                        return $phpType;
                    }
                    return '%%' . $phpType;
                }
            },
            NameMap::TAG_COMMAND
        );
    }

    public function testTypeToNameReturnAlias(): void
    {
        self::assertSame(
            'mock_message_2',
            $this->map->fromPhpType(MockMessage2::class, NameMap::TAG_COMMAND)
        );
    }

    public function testTypeToNameReturnStrategyIfNoAlias(): void
    {
        self::assertSame(
            '%%' . MockMessage1::class,
            $this->map->fromPhpType(MockMessage1::class, NameMap::TAG_COMMAND)
        );
    }

    public function testTypeToNameFallsBackOnPassthrough(): void
    {
        self::assertSame(
            MockMessage1::class,
            $this->map->fromPhpType(MockMessage1::class, NameMap::TAG_EVENT)
        );
    }

    public function testNameToTypeReturnWorksWithAllAliases(): void
    {
        self::assertSame(
            MockMessage2::class,
            $this->map->toPhpType('mock_message_2', NameMap::TAG_COMMAND)
        );
        self::assertSame(
            MockMessage2::class,
            $this->map->toPhpType('mock_2', NameMap::TAG_COMMAND)
        );
        self::assertSame(
            MockMessage2::class,
            $this->map->toPhpType('mock_2_2', NameMap::TAG_COMMAND)
        );
    }

    public function testNameToTypeReturnSameValueIfAlreadyAType(): void
    {
        self::assertSame(
            MockMessage2::class,
            $this->map->toPhpType(MockMessage2::class, NameMap::TAG_COMMAND)
        );
    }

    public function testNameToTypeReturnStrategyIfNoAlias(): void
    {
        self::assertSame(
            MockMessage1::class,
            $this->map->toPhpType('%%' . MockMessage1::class, NameMap::TAG_COMMAND)
        );
    }

    public function testNameToTypeFallsBackOnPassthrough(): void
    {
        self::assertSame(
            MockMessage1::class,
            $this->map->toPhpType(MockMessage1::class, NameMap::TAG_EVENT)
        );
    }
}
