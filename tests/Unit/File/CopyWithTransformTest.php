<?php declare(strict_types=1);

namespace Tests\Unit\File;

use Clvarley\Utility\File;
use Clvarley\Utility\Stream;
use Override;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\UsesClass;

use function touch;
use function unlink;

#[CoversMethod(File::class, 'copyWithTransform')]
#[CoversMethod(File::class, 'openHandle')]
#[UsesClass(Stream::class)]
final class CopyWithTransformTest extends FileTestCase
{
    private const DEST_FILE = __DIR__ . '/list.md';

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        touch(self::DEST_FILE);
    }

    #[Override]
    protected function tearDown(): void
    {
        parent::tearDown();

        unlink(self::DEST_FILE);
    }

    public function testCanCopyFileWithTransform(): void
    {
        $expectedContent = <<<TXT
        * one
        * two
        * three
        TXT;

        $toList = static fn ($handle): string => '* ' . fgets($handle);

        $success = File::copyWithTransform(
            self::TEST_FILE,
            self::DEST_FILE,
            $toList,
        );

        self::assertTrue($success);
        self::assertStringEqualsFile(self::DEST_FILE, $expectedContent);
    }
}
