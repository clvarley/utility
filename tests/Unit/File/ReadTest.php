<?php declare(strict_types=1);

namespace Tests\Unit\File;

use Clvarley\Utility\File;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\RequiresOperatingSystemFamily;

use function escapeshellarg;
use function exec;
use function feof;
use function fgets;
use function trim;

#[CoversMethod(File::class, 'read')]
#[CoversMethod(File::class, 'usingLock')]
#[CoversMethod(File::class, 'openHandle')]
final class ReadTest extends FileTestCase
{
    public function testCanReadFileContents(): void
    {
        $result = File::read(
            self::TEST_FILE,
            static function ($handle): array {
                $lines = [];
                while (!feof($handle)) {
                    $lines[] = trim(fgets($handle));
                }
                return $lines;
            },
        );

        self::assertSame(['one', 'two', 'three'], $result);
    }

    #[RequiresOperatingSystemFamily('Linux')]
    public function testCanReadUsingFileLock(): void
    {
        File::read(self::TEST_FILE, static function ($handle): void {
            exec(
                'flock --exclusive --nonblock ' . escapeshellarg(self::TEST_FILE),
                result_code: $resultCode
            );

            self::assertTrue(0 !== $resultCode);
        }, true);
    }
}
