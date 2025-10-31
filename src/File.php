<?php declare(strict_types=1);

namespace Clvarley\Utility;

use Generator;

use function fclose;
use function feof;
use function flock;
use function fopen;
use function is_readable;

use const LOCK_EX;
use const LOCK_SH;
use const LOCK_UN;

/**
 * @api
 */
final class File
{
    /**
     * @template T
     *
     * @param non-empty-string $filePath
     * @param callable(resource):T $callback
     * @param bool $useLock
     *
     * @return T|null
     */
    public static function read(
        string $filePath,
        callable $callback,
        bool $useLock = false,
    ): mixed {
        $handle = self::openHandle($filePath, 'r');

        if (null === $handle) {
            return null;
        }

        try {
            $result = $useLock
                ? self::usingLock($handle, $callback, LOCK_SH)
                : $callback($handle);
        } finally {
            fclose($handle);
        }

        return $result;
    }

    /**
     * @template TKey of array-key
     * @template TVal
     *
     * @param non-empty-string $filePath
     * @param callable(resource):iterable<TKey, TVal> $callback
     * @param bool $useLock
     *
     * @return Generator<TKey, TVal>
     */
    public static function readAsIterator(
        string $filePath,
        callable $callback,
        bool $useLock = false,
    ): Generator {
        $handle = self::openHandle($filePath, 'r');

        if (null === $handle) {
            return;
        }

        try {
            yield from $useLock
                ? self::usingLockYield($handle, $callback, LOCK_SH)
                : $callback($handle);
        } finally {
            fclose($handle);
        }
    }

    /**
     * @template T
     *
     * @param non-empty-string $filePath
     * @param callable(resource):T $callback
     * @param bool $useLock
     *
     * @return Generator<int, T>
     */
    public static function readToEnd(
        string $filePath,
        callable $callback,
        bool $useLock = false,
    ): Generator {
        yield from self::readAsIterator(
            $filePath,
            static function ($handle) use ($callback): Generator {
                while (!feof($handle)) {
                    yield $callback($handle);
                }
            },
            $useLock,
        );
    }

    /**
     * @param non-empty-string $filePath
     * @param callable(resource):void $callback
     * @param bool $useLock
     *
     * @return bool
     */
    public static function write(
        string $filePath,
        callable $callback,
        bool $useLock = false,
    ): bool {
        $handle = self::openHandle($filePath, 'w');

        if (null === $handle) {
            return false;
        }

        try {
            $useLock
                ? self::usingLock($handle, $callback, LOCK_EX)
                : $callback($handle);
        } finally {
            fclose($handle);
        }

        return true;
    }

    /**
     * @param non-empty-string $filePath
     * @param callable(resource):void $callback
     * @param bool $useLock
     *
     * @return bool
     */
    public static function append(
        string $filePath,
        callable $callback,
        bool $useLock = false,
    ): bool {
        $handle = self::openHandle($filePath, 'a');

        if (null === $handle) {
            return false;
        }

        try {
            $useLock
                ? self::usingLock($handle, $callback, LOCK_EX)
                : $callback($handle);
        } finally {
            fclose($handle);
        }

        return true;
    }

    /**
     * @param non-empty-string $filePath
     * @param callable(resource):string $callback
     *
     * @return bool
     */
    public static function transform(
        string $filePath,
        callable $callback,
    ): bool {
        $handle = self::openHandle($filePath, 'r+');
        $temp = fopen('php://temp', 'w+');

        if (null === $handle || false === $temp) {
            return false;
        }

        try {
            return self::usingLock(
                $handle,
                static function ($handle) use ($temp, $callback): bool {
                    Stream::copyWithTransform($handle, $temp, $callback);

                    return Stream::replace($handle, $temp);
                },
                LOCK_EX,
            );
        } finally {
            fclose($handle);
            fclose($temp);
        }
    }

    /**
     * @template T
     *
     * @param resource $fileHandle
     * @param callable(resource):T $callback
     * @param int $lockType
     *
     * @return T
     */
    public static function usingLock(
        $fileHandle,
        callable $callback,
        int $lockType = LOCK_SH,
    ): mixed {
        flock($fileHandle, $lockType);
        try {
            $result = $callback($fileHandle);
        } finally {
            flock($fileHandle, LOCK_UN);
        }

        return $result;
    }

    /**
     * @template TKey of array-key
     * @template TVal
     *
     * @param resource $fileHandle
     * @param callable(resource):iterable<TKey, TVal> $callback
     * @param int $lockType
     *
     * @return Generator<TKey, TVal>
     */
    public static function usingLockYield(
        $fileHandle,
        callable $callback,
        int $lockType,
    ): Generator {
        flock($fileHandle, $lockType);
        try {
            yield from $callback($fileHandle);
        } finally {
            flock($fileHandle, LOCK_UN);
        }
    }

    /**
     * @param non-empty-string $filePath
     *
     * @return resource|null
     */
    private static function openHandle(string $filePath, string $mode): mixed
    {
        if (!is_readable($filePath)) {
            return null;
        }

        $handle = fopen($filePath, $mode);

        if (false === $handle) {
            return null;
        }

        return $handle;
    }

    /**
     * @param non-empty-string $from
     * @param non-empty-string $to
     * @param callable(resource):string $transform
     *
     * @return bool
     */
    private static function copyWithTransform(
        string $from,
        string $to,
        callable $transform,
    ): bool {
        $source = self::openHandle($from, 'r');
        $target = self::openHandle($to, 'w');

        if (null === $source || null === $target) {
            return false;
        }

        try {
            Stream::copyWithTransform($source, $target, $transform);
        } finally {
            fclose($source);
            fclose($target);
        }

        return true;
    }
}
