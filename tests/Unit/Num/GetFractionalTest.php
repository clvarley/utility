<?php declare(strict_types=1);

namespace Tests\Unit\Num;

use Clvarley\Utility\Num;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;

use function round;

#[CoversMethod(Num::class, 'getFractional')]
final class GetFractionalTest extends TestCase
{
    public function testCanGetFractionalComponentOfNumber(): void
    {
        // intentional: Will always have to counteract floating point errors
        self::assertSame(0.5, round(Num::getFractional(12.5), 1));
        self::assertSame(0.321, round(Num::getFractional(54.321), 3));
        self::assertSame(0.0, Num::getFractional(100));
        self::assertSame(-0.25, round(Num::getFractional(-631.25), 2));
    }
}
