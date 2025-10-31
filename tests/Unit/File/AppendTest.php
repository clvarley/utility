<?php declare(strict_types=1);

namespace Tests\Unit\File;

use Clvarley\Utility\File;
use PHPUnit\Framework\Attributes\CoversMethod;

use function fwrite;

#[CoversMethod(File::class, 'append')]
#[CoversMethod(File::class, 'usingLock')]
#[CoversMethod(File::class, 'openHandle')]
final class AppendTest extends FileTestCase
{
    public function testCanAppendToFile(): void
    {
        $newContent = <<<TXT
        three
        four
        five
        TXT;

        $success = File::append(
            self::TEST_FILE,
            static function ($handle) use ($newContent): void {
                fwrite($handle, $newContent);
            },
        );

        self::assertTrue($success);
        self::assertStringEqualsFile(
            self::TEST_FILE,
            self::FILE_CONTENT . $newContent,
        );
    }
}
