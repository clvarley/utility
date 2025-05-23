<?php declare(strict_types=1);

namespace Clvarley\Utility;

use function is_object;

final class Debug
{
    /**
     * Return the fully qualified name of a given object.
     *
     * @template T of object
     *
     * @param T|class-string<T> $subject
     *
     * @return class-string<T>
     */
    public static function getClassName(object|string $subject): string
    {
        return is_object($subject) ? $subject::class : $subject;
    }
}
