<?php declare(strict_types=1);

namespace Tests\Unit\Str;

use Clvarley\Utility\Str;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversMethod(Str::class, 'pascalCase')]
final class PascalCaseTest extends StringTestCase
{
    #[DataProvider('provideExampleStrings')]
    public function testCanPascalCaseString(string $subject, string $expected): void
    {
        self::assertSame($expected, Str::pascalCase($subject));
    }

    public function testWillCollapseLeadingWhitespace(): void
    {
        self::assertSame('HiThere', Str::pascalCase('  - hi there'));
    }

    public function testWillCollapseTrailingWhitespace(): void
    {
        self::assertSame('ByeThen', Str::pascalCase('bye then-  '));
    }

    public static function provideExampleStrings(): iterable
    {
        yield 'single word' => [
            'hello', 'Hello',
        ];
        yield 'phrase' => [
            'Lorem ipsum et', 'LoremIpsumEt',
        ];
        yield 'hyphenated string' => [
            'profile-photo-medium', 'ProfilePhotoMedium',
        ];
        yield 'underscored string' => [
            'final_final_v2', 'FinalFinalV2',
        ];
        yield 'multiple spaces' => [
            'yes   no', 'YesNo',
        ];
    }
}
