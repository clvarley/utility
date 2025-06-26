<?php declare(strict_types=1);

namespace Tests\Unit\Arr;

use Clvarley\Utility\Arr;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesMethod;
use Tests\Unit\Arr\ArrayTestCase;

#[CoversMethod(Arr::class, 'splitAtKey')]
#[UsesMethod(Arr::class, 'keyOffset')]
#[UsesMethod(Arr::class, 'splitAtOffset')]
final class SplitAtKeyTest extends ArrayTestCase
{
    public function testCanSplitArrayAfterKey(): void
    {
        [$head, $tail] = Arr::splitAtKey(
            self::EXAMPLE_VALUES,
            'username',
            Arr::SPLIT_AFTER,
        );

        self::assertSame([
            'name',
            'John',
            'age' => 42,
            ['php', 'js', 'html'],
            'blue',
            'username' => 'j.smith',
        ], $head);
        self::assertSame([
            3.14,
            '@john',
            true,
        ], $tail);
    }

    public function testCanSplitArrayBeforeKey(): void
    {
        [$head, $tail] = Arr::splitAtKey(
            self::EXAMPLE_VALUES,
            11,
            Arr::SPLIT_BEFORE,
        );

        self::assertSame([
            'name',
            'John',
            'age' => 42,
            ['php', 'js', 'html'],
        ], $head);
        self::assertSame([
            'blue',
            'username' => 'j.smith',
            3.14,
            '@john',
            true,
        ], $tail);
    }

    #[TestDox('Will return empty tail on non-existent int key')]
    public function testWillReturnEmptyTailOnNonExistentIntKey(): void
    {
        [$head, $tail] = Arr::splitAtKey(
            self::EXAMPLE_VALUES,
            42,
            Arr::SPLIT_AFTER,
        );

        self::assertEqualsCanonicalizing($head, self::EXAMPLE_VALUES);
        self::assertIsArray($tail);
        self::assertEmpty($tail);
    }

    #[TestDox('Will return empty tail on non-existent string key')]
    public function testWillReturnEmptyTailOnNonExistentStringKey(): void
    {
        [$head, $tail] = Arr::splitAtKey(
            self::EXAMPLE_VALUES,
            'hobbies',
            Arr::SPLIT_BEFORE,
        );

        self::assertEqualsCanonicalizing(self::EXAMPLE_VALUES, $head);
        self::assertIsArray($tail);
        self::assertEmpty($tail);
    }
}
