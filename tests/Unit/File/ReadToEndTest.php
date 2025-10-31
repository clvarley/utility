<?php declare(strict_types=1);

namespace Tests\Unit\File;

use Clvarley\Utility\File;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\UsesMethod;

use function fgets;
use function iterator_to_array;
use function trim;

#[CoversMethod(File::class, 'readToEnd')]
#[CoversMethod(File::class, 'usingLockYield')]
#[CoversMethod(File::class, 'openHandle')]
#[UsesMethod(File::class, 'readAsIterator')]
final class ReadToEndTest extends FileTestCase
{
    public function testCanReadFileContentsIterativelyToEnd(): void
    {
        $callback = static fn ($handle): string => trim(fgets($handle));

        $result = iterator_to_array(
            File::readToEnd(self::TEST_FILE, $callback),
        );

        self::assertSame(['one', 'two', 'three'], $result);
    }
}
