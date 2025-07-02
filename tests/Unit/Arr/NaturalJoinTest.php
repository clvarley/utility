<?php declare(strict_types=1);

namespace Tests\Unit\Arr;

use Clvarley\Utility\Arr;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Arr::class, 'naturalJoin')]
final class NaturalJoinTest extends TestCase
{
    public function testCanCreateNaturalLanguageListFromArrayItems(): void
    {
        self::assertSame(
            'red, green and blue',
            Arr::naturalJoin(['red', 'green', 'blue']),
        );
    }

    public function testCanCreateNaturalLanguageListFromStringableItems(): void
    {
        $john = new class () {
            public function __toString(): string
            {
                return 'John';
            }
        };

        self::assertSame(
            'Jack, John and Jane',
            Arr::naturalJoin(['Jack', $john, 'Jane']),
        );
    }

    public function testCanChangeSeparatorStringWhenJoiningArray(): void
    {
        self::assertSame(
            'Choc-o-late and milk',
            Arr::naturalJoin(['Choc', 'o', 'late', 'milk'], separator: '-'),
        );
    }

    public function testCanChangeConjunctionWhenJoiningArray(): void
    {
        self::assertSame(
            'north, south, east or west',
            Arr::naturalJoin(['north', 'south', 'east', 'west'], conjunction: ' or '),
        );
    }

    public function testCanChangeSeparatorAndConjunctionWhenJoiningArray(): void
    {
        self::assertSame(
            '10 * 10 * 10 = 1000',
            Arr::naturalJoin([10, 10, 10, 1000], ' * ', ' = '),
        );
    }

    public function testWillOnlyReturnItemOnSingleItemArray(): void
    {
        self::assertSame('42', Arr::naturalJoin([42]));
    }

    public function testWillConjoinItemsOnTwoItemArray(): void
    {
        self::assertSame('up and down', Arr::naturalJoin(['up', 'down']));
    }
}
