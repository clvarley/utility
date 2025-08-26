<?php declare(strict_types=1);

namespace Tests\Unit\Arr;

use Clvarley\Utility\Arr;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversMethod(Arr::class, 'mergeAfterKey')]
#[UsesClass(Arr::class, 'keyOffset')]
#[UsesClass(Arr::class, 'splitAtOffset')]
final class MergeAfterKeyTest extends ArrayTestCase
{
    public function testCanMergeValuesAfterStringKey(): void
    {
        $subject = Arr::mergeAfterKey(self::EXAMPLE_VALUES, 'username', [
            'email' => 'j.smith@example.com',
            'mobile' => '0123456789',
            'fax' => null,
        ]);

        self::assertSame([
            'name',
            'John',
            'age' => 42,
            ['php', 'js', 'html'],
            'blue',
            'username' => 'j.smith',
            'email' => 'j.smith@example.com',
            'mobile' => '0123456789',
            'fax' => null,
            3.14,
            '@john',
            true,
        ], $subject);
    }

    public function testCanMergeValuesAfterIntKey(): void
    {
        $subject = Arr::mergeAfterKey(self::EXAMPLE_VALUES, 11, [
            'green',
            null,
            'hobbies' => ['reading', 'hiking'],
        ]);

        self::assertSame([
            'name',
            'John',
            'age' => 42,
            ['php', 'js', 'html'],
            'blue',
            'green',
            null,
            'hobbies' => ['reading', 'hiking'],
            'username' => 'j.smith',
            3.14,
            '@john',
            true,
        ], $subject);
    }

    #[TestDox('Will merge into end on non-existent string key')]
    public function testWillMergeIntoEndOnNonExistentStringKey(): void
    {
        $subject = Arr::mergeAfterKey(self::EXAMPLE_VALUES, 'friends', [
            'enemies' => 'many',
            0 => 1,
        ]);

        self::assertSame([
            'name',
            'John',
            'age' => 42,
            ['php', 'js', 'html'],
            'blue',
            'username' => 'j.smith',
            3.14,
            '@john',
            true,
            'enemies' => 'many',
            1,
        ], $subject);
    }

    #[TestDox('Will merge into end on non-existent int key')]
    public function testWillMergeIntoEndOnNonExistentIntKey(): void
    {
        $subject = Arr::mergeAfterKey(self::EXAMPLE_VALUES, 11, [
            'red',
            0.57721,
        ]);

        self::assertSame([
            'name',
            'John',
            'age' => 42,
            ['php', 'js', 'html'],
            'blue',
            'red',
            0.57721,
            'username' => 'j.smith',
            3.14,
            '@john',
            true,
        ], $subject);
    }
}
