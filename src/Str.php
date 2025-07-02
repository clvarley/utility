<?php declare(strict_types=1);

namespace Clvarley\Utility;

use function str_replace;
use function str_split;
use function strlen;
use function strpos;
use function substr;
use function ucwords;

/**
 * @api
 */
final class Str
{
    public const SPLIT_BEFORE = 0;
    public const SPLIT_AFTER = 1;

    private const WORD_SEPERATORS = " \t\r\n\f\v-_";

    /**
     * Split a string in half at the first occurance of `$needle`.
     *
     * If the subject string does not contain `$needle` the return value will
     * be `[$subject, '']`.
     *
     * The difference between this function and {@see Str::splitAround()} is
     * that the result will also include `$needle`. For example, given the
     * subject `"Jack & Jill"` and the needle `" & "` the result will be the
     * following:
     *
     * ```php
     * <?php
     *
     * use Clvarley\Utility\Str;
     *
     * Str::splitAt('Jack & Jill', ' & ');     // ['Jack & ', 'Jill']
     * Str::splitAround('Jack & Jill', ' & '); // ['Jack', 'Jill']
     * ```
     *
     * If you don't want the needle to appear in the output use
     * {@see Str::splitAround()} instead.
     *
     * @pure
     *
     * @param string $subject
     * @param string $needle       The substring to search for.
     * @param self::SPLIT_* $where Whether to split before or after `$needle`.
     *
     * @psalm-return ($subject is non-empty-string
     *     ? ($where is positive-int
     *         ? list{0: non-empty-string, 1: string}
     *         : list{0: string, 1: string})
     *     : list{0: string, 1: string}
     * )
     * @return list{0: string, 1: string}
     */
    public static function splitAt(
        string $subject,
        string $needle,
        int $where = self::SPLIT_AFTER,
    ): array {
        $offset = strpos($subject, $needle);

        if (false === $offset) {
            return [$subject, ''];
        }

        if ($where) {
            $offset += strlen($needle);
        }

        $head = substr($subject, 0, $offset);
        $tail = substr($subject, $offset);

        return [$head, $tail];
    }

    /**
     * Split a string in half around the first occurance of `$needle`.
     *
     * If the subject string does not contain `$needle` the return value will
     * be `[$subject, '']`.
     *
     * The difference between this function and {@see Str::splitAt()} is that
     * the result does not contain the `$needle` string. For example, given
     * the subject `"Jack & Jill"` and the needle `" & "` the result will be
     * the following:
     *
     * ```php
     * <?php
     *
     * use Clvarley\Utility\Str;
     *
     * Str::splitAround('Jack & Jill', ' & '); // ['Jack', 'Jill']
     * Str::splitAt('Jack & Jill', ' & ');     // ['Jack & ', 'Jill']
     * ```
     *
     * This function is therefore analogous to using `explode(...)` with
     * `$limit = 2` but with the guarantee that the result will always be a 2
     * element array.
     *
     * If you want the output to also contain the needle use
     * {@see Str::splitAt()} instead.
     *
     * @pure
     *
     * @param string $subject
     * @param string $needle The substring to search for.
     *
     * @return list{0: string, 1: string}
     */
    public static function splitAround(string $subject, string $needle): array
    {
        $offset = strpos($subject, $needle);

        if (false === $offset) {
            return [$subject, ''];
        }

        $head = substr($subject, 0, $offset);
        $tail = substr($subject, $offset + strlen($needle));

        return [$head, $tail];
    }

    /**
     * Transform a string to PascalCase and collapse whitespaces.
     *
     * @pure
     *
     * @param string $subject
     * @param non-empty-string $separators Word boundary characters.
     *
     * @return string
     */
    public static function pascalCase(
        string $subject,
        string $separators = self::WORD_SEPERATORS,
    ): string {
        $subject = ucwords($subject, $separators);

        return str_replace(str_split($separators), '', $subject);
    }
}
