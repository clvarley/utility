<?php declare(strict_types=1);

namespace Tests\Unit\File;

use Clvarley\Utility\File;
use Clvarley\Utility\Stream;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\UsesClass;

use function fgets;
use function strtr;

#[CoversMethod(File::class, 'transform')]
#[CoversMethod(File::class, 'usingLock')]
#[CoversMethod(File::class, 'openHandle')]
#[UsesClass(Stream::class)]
final class TransformTest extends FileTestCase
{
    public function testCanTransformFileInSitu(): void
    {
        $expectedContent = <<<TXT
        one
        four
        nine
        TXT;

        $squared = static function ($handle): string {
            return strtr(fgets($handle), [
                'one' => 'one',
                'two' => 'four',
                'three' => 'nine',
            ]);
        };

        $success = File::transform(self::TEST_FILE, $squared);

        self::assertTrue($success);
        self::assertStringEqualsFile(self::TEST_FILE, $expectedContent);
    }
}
