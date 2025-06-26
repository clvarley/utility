<?php declare(strict_types=1);

namespace Tests\Unit\Arr;

use Clvarley\Utility\Arr;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversMethod(Arr::class, 'pickKeys')]
final class PickKeysTest extends ArrayTestCase
{
    protected const EXAMPLE_VALUES = [
        'developer',
        'name' => 'John',
        'age' => 42,
        'height' => 1.82,
        'email' => 'j.smith@example.com',
        'mobile' => '0123456789',
        ['php', 'html', 'css'],
        3.14,
    ];

    public function testWillOnlyReturnSpecifiedStringKeys(): void
    {
        $subject = Arr::pickKeys(self::EXAMPLE_VALUES, ['name', 'email']);

        self::assertSame([
            'name' => 'John',
            'email' => 'j.smith@example.com',
        ], $subject);
    }

    public function testWillOnlyReturnSpecifiedIntKeys(): void
    {
        $subject = Arr::pickKeys(self::EXAMPLE_VALUES, [0, 1]);

        self::assertSame([
            'developer',
            ['php', 'html', 'css'],
        ], $subject);
    }
}
