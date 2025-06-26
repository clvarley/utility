<?php declare(strict_types=1);

namespace Tests\Unit\Arr;

use Clvarley\Utility\Arr;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversMethod(Arr::class, 'dropKeys')]
final class DropKeysTest extends ArrayTestCase
{
    public function testWillDropSpecifiedStringKeys(): void
    {
        $subject = Arr::dropKeys(self::EXAMPLE_VALUES, ['age', 'username']);

        self::assertSame([
            'name',
            'John',
            ['php', 'js', 'html'],
            11 => 'blue',
            3.14,
            '@john',
            117 => true,
        ], $subject);
    }

    public function testWillDropSpecifiedIntKeys(): void
    {
        $subject = Arr::dropKeys(self::EXAMPLE_VALUES, [0, 1, 11]);

        self::assertSame([
            'age' => 42,
            2 => ['php', 'js', 'html'],
            'username' => 'j.smith',
            12 => 3.14,
            13 => '@john',
            117 => true,
        ], $subject);
    }
}
