<?php declare(strict_types=1);

namespace Tests\Unit\Arr;

use Clvarley\Utility\Arr;
use PHPUnit\Framework\Attributes\CoversMethod;
use Tests\Unit\Arr\ArrayTestCase;

#[CoversMethod(Arr::class, 'splitAtOffset')]
final class SplitAtOffsetTest extends ArrayTestCase
{
    public function testCanSplitArrayAfterOffset(): void
    {
        [$head, $tail] = Arr::splitAtOffset(
            self::EXAMPLE_VALUES,
            4,
            Arr::SPLIT_AFTER,
        );

        self::assertSame([
            'name',
            'John',
            'age' => 42,
            ['php', 'js', 'html'],
            'blue',
        ], $head);
        self::assertSame([
            'username' => 'j.smith',
            3.14,
            '@john',
            true,
        ], $tail);
    }

    public function testCanSplitArrayBeforeOffset(): void
    {
        [$head, $tail] = Arr::splitAtOffset(
            self::EXAMPLE_VALUES,
            3,
            Arr::SPLIT_BEFORE,
        );

        self::assertSame([
            'name',
            'John',
            'age' => 42,
        ], $head);
        self::assertSame([
            ['php', 'js', 'html'],
            'blue',
            'username' => 'j.smith',
            3.14,
            '@john',
            true,
        ], $tail);
    }

    public function testWillReturnEmptyTailOnOutOfBoundsOffset(): void
    {
        [$head, $tail] = Arr::splitAtOffset(
            self::EXAMPLE_VALUES,
            20,
            Arr::SPLIT_AFTER,
        );

        self::assertEqualsCanonicalizing(self::EXAMPLE_VALUES, $head);
        self::assertIsArray($tail);
        self::assertEmpty($tail);
    }
}
