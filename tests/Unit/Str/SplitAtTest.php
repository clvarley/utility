<?php declare(strict_types=1);

namespace Tests\Unit\Str;

use Clvarley\Utility\Str;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversMethod(Str::class, 'splitAt')]
final class SplitAtTest extends StringTestCase
{
    public function testCanSplitStringAfterSingleCharacter(): void
    {
        [$head, $tail] = Str::splitAt(
            self::EXAMPLE_EMAIL,
            '@',
            Str::SPLIT_AFTER,
        );

        self::assertSame('j.smith@', $head);
        self::assertSame('example.com', $tail);
    }

    public function testCanSplitStringAfterSubstring(): void
    {
        [$head, $tail] = Str::splitAt(
            self::EXAMPLE_COLOURS,
            'and',
            Str::SPLIT_AFTER,
        );

        self::assertSame('green and', $head);
        self::assertSame(' blue', $tail);
    }

    public function testCanSplitStringBeforeSingleCharacter(): void
    {
        [$head, $tail] = Str::splitAt(
            self::EXAMPLE_EMAIL,
            '@',
            Str::SPLIT_BEFORE,
        );

        self::assertSame('j.smith', $head);
        self::assertSame('@example.com', $tail);
    }

    public function testCanSplitStringBeforeSubstring(): void
    {
        [$head, $tail] = Str::splitAt(
            self::EXAMPLE_COLOURS,
            'and',
            Str::SPLIT_BEFORE,
        );

        self::assertSame('green ', $head);
        self::assertSame('and blue', $tail);
    }

    public function testWillReturnEmptyTailIfSubstringNotInSubject(): void
    {
        [$head, $tail] = Str::splitAt(
            self::EXAMPLE_EMAIL,
            '#',
            Str::SPLIT_AFTER,
        );

        self::assertSame(self::EXAMPLE_EMAIL, $head);
        self::assertSame('', $tail);
    }

    public function testWillReturnEmptyTailIfSubstringAtEndOfSubject(): void
    {
        [$head, $tail] = Str::splitAt(
            self::EXAMPLE_EMAIL,
            '.com',
            Str::SPLIT_AFTER,
        );

        self::assertSame(self::EXAMPLE_EMAIL, $head);
        self::assertSame('', $tail);
    }

    public function testWillReturnEmptyHeadIfSubstringAtStartOfString(): void
    {
        [$head, $tail] = Str::splitAt(
            self::EXAMPLE_COLOURS,
            'green',
            Str::SPLIT_BEFORE,
        );

        self::assertSame('', $head);
        self::assertSame(self::EXAMPLE_COLOURS, $tail);
    }
}
