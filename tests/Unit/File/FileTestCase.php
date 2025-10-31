<?php declare(strict_types=1);

namespace Tests\Unit\File;

use PHPUnit\Framework\TestCase;

use function file_put_contents;
use function unlink;

abstract class FileTestCase extends TestCase
{
    protected const TEST_FILE = __DIR__ . '/test.txt';

    protected const FILE_CONTENT = <<<TXT
    one
    two
    three
    TXT;

    protected function setUp(): void
    {
        file_put_contents(self::TEST_FILE, self::FILE_CONTENT);
    }

    protected function tearDown(): void
    {
        unlink(self::TEST_FILE);
    }
}
