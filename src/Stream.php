<?php declare(strict_types=1);

namespace Clvarley\Utility;

use function feof;
use function fseek;
use function ftruncate;
use function fwrite;
use function in_array;
use function rtrim;
use function stream_copy_to_stream;
use function stream_get_meta_data;

use const SEEK_SET;

/**
 * @api
 */
final class Stream
{
    private const READING_MODES = ['r', 'r+', 'w+', 'a+', 'x+', 'c+'];
    private const WRITING_MODES = ['r+', 'w', 'w+', 'a', 'a+', 'x', 'x+', 'c', 'c+'];

    /**
     * @param resource $source
     * @param resource $destination
     * @param callable(resource):string $transform
     */
    public static function copyWithTransform(
        $source,
        $destination,
        callable $transform
    ): void {
        while (!feof($source)) {
            fwrite($destination, $transform($source));
        }
    }

    /**
     * @param resource $destination
     * @param resource $source
     *
     * @return bool
     */
    public static function replace($destination, $source): bool
    {
        if (self::isSeekable($destination)) {
            fseek($destination, 0, SEEK_SET);
        }
        if (self::isSeekable($source)) {
            fseek($source, 0, SEEK_SET);
        }

        $written = stream_copy_to_stream($source, $destination);

        if (false === $written) {
            return false;
        }

        ftruncate($destination, $written);

        return true;
    }

    /**
     * @pure
     *
     * @param resource $stream
     *
     * @return bool
     */
    public static function isSeekable($stream): bool
    {
        return !empty(stream_get_meta_data($stream)['seekable']);
    }

    /**
     * @pure
     *
     * @param resource $stream
     *
     * @return bool
     */
    public static function isReadable($stream): bool
    {
        return in_array(
            rtrim(stream_get_meta_data($stream)['mode'], 'bt'),
            self::READING_MODES,
            true,
        );
    }

    /**
     * @pure
     *
     * @param resource $stream
     *
     * @return bool
     */
    public static function isWritable($stream): bool
    {
        return in_array(
            rtrim(stream_get_meta_data($stream)['mode'], 'bt'),
            self::WRITING_MODES,
            true,
        );
    }
}
