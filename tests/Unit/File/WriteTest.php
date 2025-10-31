<?php declare(strict_types=1);

namespace Tests\Unit\File;

use Clvarley\Utility\File;
use PHPUnit\Framework\Attributes\CoversMethod;

use function fwrite;

#[CoversMethod(File::class, 'write')]
#[CoversMethod(File::class, 'usingLock')]
#[CoversMethod(File::class, 'openHandle')]
final class WriteTest extends FileTestCase
{
    public function testCanWriteToFile(): void
    {
        $newContent = <<<TXT
        uno
        dos
        tres
        TXT;

        $success = File::write(
            self::TEST_FILE,
            static function ($handle) use ($newContent): void {
                fwrite($handle, $newContent);
            },
        );

        self::assertTrue($success);
        self::assertStringEqualsFile(self::TEST_FILE, $newContent);
    }
}
