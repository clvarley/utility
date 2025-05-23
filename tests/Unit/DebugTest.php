<?php declare(strict_types=1);

namespace Tests\Unit;

use Clvarley\Utility\Debug;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(Debug::class)]
final class DebugTest extends TestCase
{
    #[DataProvider('provideExampleClasses')]
    public function testCanDetermineClassName(object|string $subject, string $expected): void
    {
        self::assertSame($expected, Debug::getClassName($subject));
    }

    /**
     * @return iterable<string, array{0: class-string|object, 1: string}>
     */
    public static function provideExampleClasses(): iterable
    {
        yield 'global class (string)' => [
            DateTimeImmutable::class,
            'DateTimeImmutable',
        ];
        yield 'global class (object)' => [
            new DateTimeImmutable(),
            'DateTimeImmutable',
        ];
        yield 'namespaced class (string)' => [
            self::class,
            'Tests\\Unit\\DebugTest',
        ];
        yield 'namespaced class (object)' => [
            new self('example'),
            'Tests\\Unit\\DebugTest',
        ];
    }
}
