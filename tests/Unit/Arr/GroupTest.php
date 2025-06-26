<?php declare(strict_types=1);

namespace Tests\Unit\Arr;

use Clvarley\Utility\Arr;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversMethod(Arr::class, 'group')]
final class GroupTest extends ArrayTestCase
{
    public function testCanGroupElementsWithStringKeys(): void
    {
        $grouped = Arr::group(
            [1, 3, 4, 7, 8, 2],
            static fn (int $value): string => $value % 2 === 0 ? 'even' : 'odd',
        );

        self::assertEqualsCanonicalizing([
            'even' => [4, 8, 2],
            'odd' => [1, 3, 7],
        ], $grouped);
    }

    public function testCanGroupElementsWithIntKeys(): void
    {
        $grouped = Arr::group(
            [
                ['name' => 'John', 'floor' => 2],
                ['name' => 'Jill', 'floor' => 1],
                ['name' => 'Jack', 'floor' => 2],
                ['name' => 'Jane', 'floor' => 3],
            ],
            static fn (array $value): int => $value['floor'],
        );

        self::assertEqualsCanonicalizing([
            1 => [['name' => 'Jill', 'floor' => 1]],
            2 => [['name' => 'John', 'floor' => 2], ['name' => 'Jack', 'floor' => 2]],
            3 => [['name' => 'Jane', 'floor' => 3]],
        ], $grouped);
    }
}
