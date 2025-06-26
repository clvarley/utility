<?php declare(strict_types=1);

namespace Tests\Unit\Arr;

use Clvarley\Utility\Arr;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesMethod;

#[CoversMethod(Arr::class, 'insertAfterKey')]
#[UsesMethod(Arr::class, 'keyOffset')]
#[UsesMethod(Arr::class, 'splitAtOffset')]
final class InsertAfterKeyTest extends ArrayTestCase
{
    public function testCanInsertValueAfterStringKey(): void
    {
        $subject = Arr::insertAfterKey(
            self::EXAMPLE_VALUES,
            'username',
            'p@ssword'
        );

        self::assertSame([
            'name',
            'John',
            'age' => 42,
            ['php', 'js', 'html'],
            'blue',
            'username' => 'j.smith',
            'p@ssword',
            3.14,
            '@john',
            true,
        ], $subject);
    }

    public function testCanInsertValueAfterIntKey(): void
    {
        $subject = Arr::insertAfterKey(self::EXAMPLE_VALUES, 11, 'green');

        self::assertSame([
            'name',
            'John',
            'age' => 42,
            ['php', 'js', 'html'],
            'blue',
            'green',
            'username' => 'j.smith',
            3.14,
            '@john',
            true,
        ], $subject);
    }

    #[TestDox('Will append value to end on non-existent string key')]
    public function testWillAppendValueToEndOnNonExistentStringKey(): void
    {
        $subject = Arr::insertAfterKey(
            self::EXAMPLE_VALUES,
            'hobbies',
            'hiking'
        );

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
            'hiking',
        ], $subject);
    }

        #[TestDox('Will append value to end on non-existent int key')]
    public function testWillAppendValueToEndOnNonExistentIntKey(): void
    {
        $subject = Arr::insertAfterKey(
            self::EXAMPLE_VALUES,
            42,
            'green',
        );

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
            'green',
        ], $subject);
    }
}
