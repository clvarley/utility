<?php declare(strict_types=1);

namespace Tests\Unit\Arr;

use PHPUnit\Framework\TestCase;

abstract class ArrayTestCase extends TestCase
{
    protected const EXAMPLE_VALUES = [
        'name',
        'John',
        'age' => 42,
        ['php', 'js', 'html'],
        11 => 'blue',
        'username' => 'j.smith',
        3.14,
        '@john',
        117 => true,
    ];
}
