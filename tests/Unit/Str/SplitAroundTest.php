<?php declare(strict_types=1);

namespace Tests\Unit\Str;

use Clvarley\Utility\Str;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversMethod(Str::class, 'splitAround')]
final class SplitAroundTest extends StringTestCase
{
    public function testCanSplitStringAroundSingleCharacter(): void
    {
        [$head, $tail] = Str::splitAround(self::EXAMPLE_EMAIL, '@');

        self::assertSame('j.smith', $head);
        self::assertSame('example.com', $tail);
    }

    public function testCanSplitStringAroundSubstring(): void
    {
        [$head, $tail] = Str::splitAround(self::EXAMPLE_COLOURS, ' and ');

        self::assertSame('green', $head);
        self::assertSame('blue', $tail);
    }

    public function testWillReturnEmptyTailIfSubstringNotInSubject(): void
    {
        [$head, $tail] = Str::splitAround(self::EXAMPLE_EMAIL, '#');

        self::assertSame(self::EXAMPLE_EMAIL, $head);
        self::assertSame('', $tail);
    }

    public function testWillReturnEmptyHeadIfSubstringAtStartOfString(): void
    {
        [$head, $tail] = Str::splitAround(self::EXAMPLE_COLOURS, 'green');

        self::assertSame('', $head);
        self::assertSame(' and blue', $tail);
    }
}
