<?php declare(strict_types=1);

namespace Tests\Unit\File;

use Clvarley\Utility\File;
use PHPUnit\Framework\Attributes\CoversMethod;

use function feof;
use function fgets;
use function iterator_to_array;
use function trim;

#[CoversMethod(File::class, 'readAsIterator')]
#[CoversMethod(File::class, 'usingLockYield')]
#[CoversMethod(File::class, 'openHandle')]
final class ReadAsIteratorTest extends FileTestCase
{
    public function testCanReadFileContentsUsingGenerator(): void
    {
        $callback = static function ($handle): iterable {
            while (!feof($handle)) {
                yield trim(fgets($handle));
            }
        };

        $result = iterator_to_array(
            File::readAsIterator(self::TEST_FILE, $callback),
        );

        self::assertSame(['one', 'two', 'three'], $result);
    }
}
