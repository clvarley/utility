<?php declare(strict_types=1);

namespace Tests\Unit\Arr;

use Clvarley\Utility\Arr;
use PHPUnit\Framework\Attributes\CoversMethod;
use Tests\Unit\Arr\ArrayTestCase;

#[CoversMethod(Arr::class, 'keyOffset')]
final class KeyOffsetTest extends ArrayTestCase
{
    public function testCanGetIntKeyOffset(): void
    {
        self::assertSame(4, Arr::keyOffset(11, self::EXAMPLE_VALUES));
        self::assertSame(8, Arr::keyOffset(117, self::EXAMPLE_VALUES));

        self::assertNull(Arr::keyOffset(4, self::EXAMPLE_VALUES));
    }

    public function testCanGetStringKeyOffset(): void
    {
        self::assertSame(2, Arr::keyOffset('age', self::EXAMPLE_VALUES));
        self::assertSame(5, Arr::keyOffset('username', self::EXAMPLE_VALUES));

        self::assertNull(Arr::keyOffset('hobbies', self::EXAMPLE_VALUES));
    }
}
