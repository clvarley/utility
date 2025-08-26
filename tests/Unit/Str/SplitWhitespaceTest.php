<?php declare(strict_types=1);

namespace Tests\Unit\Str;

use Clvarley\Utility\Str;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversMethod(Str::class, 'splitWhitespace')]
final class SplitWhitespaceTest extends StringTestCase
{
    #[DataProvider('provideExampleStrings')]
    public function testCanSplitStringIntoParts(string $subject, array $expected): void
    {
        self::assertSame($expected, Str::splitWhitespace($subject));
    }

    public static function provideExampleStrings(): iterable
    {
        yield 'whitespaces' => [
            self::EXAMPLE_COLOURS, ['green', 'and', 'blue'],
        ];
        yield 'tabs' => [
            "One\tTwo\t\tThree", ['One', 'Two', 'Three'],
        ];
        yield 'newlines' => [
            "Now\nthis\r\nthen\nthat", ['Now', 'this', 'then', 'that'],
        ];
    }
}
