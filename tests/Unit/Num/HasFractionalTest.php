<?php declare(strict_types=1);

namespace Tests\Unit\Num;

use Clvarley\Utility\Num;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\UsesMethod;
use PHPUnit\Framework\TestCase;

#[CoversMethod(Num::class, 'hasFractional')]
#[UsesMethod(Num::class, 'getFractional')]
final class HasFractionalTest extends TestCase
{
    public function testCanDetermineIfNumberHasFractionalComponent(): void
    {
        self::assertFalse(Num::hasFractional(1.0));
        self::assertFalse(Num::hasFractional(-1));
        self::assertFalse(Num::hasFractional(42_424));
        self::assertFalse(Num::hasFractional(0));
        self::assertTrue(Num::hasFractional(3.14));
        self::assertTrue(Num::hasFractional(7.0000000001));
        self::assertTrue(Num::hasFractional(-0.1));
    }
}
