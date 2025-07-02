<?php declare(strict_types=1);

namespace Clvarley\Utility;

use Stringable;

use function array_diff_key;
use function array_flip;
use function array_intersect_key;
use function array_keys;
use function array_key_exists;
use function array_merge;
use function array_pop;
use function array_search;
use function array_slice;
use function count;
use function implode;

/**
 * @api
 */
final class Arr
{
    public const SPLIT_BEFORE = 0;
    public const SPLIT_AFTER = 1;

    /**
     * Determine how far into an array a given key appears.
     *
     * Returns how many elements exist before `$key` in the `$subject` or `null`
     * if the key does not appear.
     *
     * @pure
     *
     * @return null|non-negative-int
     */
    public static function keyOffset(int|string $key, array $subject): null|int
    {
        // Intentional: strings can access numeric indexes and vice-versa
        $keyOffset = array_search($key, array_keys($subject), false);

        return false === $keyOffset ? null : $keyOffset;
    }

    /**
     * Insert a single value into the array after the given key.
     *
     * @pure
     *
     * @template TKey of array-key
     * @template TVal
     *
     * @param array<TKey, TVal> $subject
     * @param TKey $key
     * @param TVal $value
     *
     * @return non-empty-array<TKey|non-negative-int, TVal>
     */
    public static function insertAfterKey(
        array $subject,
        int|string $key,
        mixed $value,
    ): array {
        $keyOffset = self::keyOffset($key, $subject);

        if (null === $keyOffset) {
            $subject[] = $value;

            return $subject;
        }

        [$head, $tail] = self::splitAtOffset(
            $subject,
            $keyOffset,
            self::SPLIT_AFTER,
        );

        $head[] = $value;

        return array_merge($head, $tail);
    }

    /**
     * Insert the elements of one array into the other after the given key.
     *
     * This can be used as an immutable stand-in for the standard library
     * function {@see array_splice()} in cases where you want to insert
     * elements.
     *
     * @pure
     *
     * @template TKey of array-key
     * @template TVal
     *
     * @param array<TKey, TVal> $subject
     * @param TKey $key
     * @param array<TKey, TVal> $values
     *
     * @psalm-return ($subject is non-empty-array
     *     ? non-empty-array<TKey, TVal>
     *     : ($values is non-empty-array
     *         ? non-empty-array<TKey, TVal>
     *         : array<TKey, TVal>)
     * )
     * @return array<TKey, TVal>
     */
    public static function mergeAfterKey(
        array $subject,
        int|string $key,
        array $values,
    ): array {
        $keyOffset = self::keyOffset($key, $subject);

        if (null === $keyOffset) {
            return array_merge($subject, $values);
        }

        [$head, $tail] = self::splitAtOffset(
            $subject,
            $keyOffset,
            self::SPLIT_AFTER,
        );

        return array_merge($head, $values, $tail);
    }

    /**
     * Split an array into two halves after `$offset` number of element.
     *
     * @pure
     *
     * @template TKey of array-key
     * @template TVal
     *
     * @param array<TKey, TVal> $subject
     * @param non-negative-int $offset How far into the array to split.
     * @param self::SPLIT_* $where     Whether to split before or after `$offset`.
     *
     * @return list{0: array<TKey, TVal>, 1: array<TKey, TVal>}
     */
    public static function splitAtOffset(
        array $subject,
        int $offset,
        int $where = self::SPLIT_AFTER,
    ): array {
        $offset += $where;

        $head = array_slice($subject, 0, $offset);
        $tail = array_slice($subject, $offset);

        return [$head, $tail];
    }

    /**
     * Split an array into two halves at the given `$key`.
     *
     * @pure
     *
     * @template TKey of array-key
     * @template TVal
     *
     * @param array<TKey, TVal> $subject
     * @param TKey $key                  The index key at which to split.
     * @param self::SPLIT_* $where       Whether to split before or after `$key`.
     *
     * @return list{0: array<TKey, TVal>, 1: array<TKey, TVal>}
     */
    public static function splitAtKey(
        array $subject,
        int|string $key,
        int $where = self::SPLIT_AFTER,
    ): array {
        $keyOffset = self::keyOffset($key, $subject);

        if (null === $keyOffset) {
            return [$subject, []];
        }

        return self::splitAtOffset($subject, $keyOffset, $where);
    }

    /**
     * Filter an array to only include the keys specified in `$keys`.
     *
     * @pure
     *
     * @template T of array
     * @template TKey as key-of<T>
     *
     * @param T $subject
     * @param non-empty-list<TKey> $keys
     *
     * @return T
     */
    public static function pickKeys(array $subject, array $keys): array
    {
        /**
         * Hack: Psalm doesn't yet support array intersection via comment, hence
         * the slightly wider return type. Other variations of the @template
         * tags cause the happy path to raise PossiblyUndefined*ArrayOffset
         * warnings.
         *
         * @psalm-var T
         */
        return array_intersect_key($subject, array_flip($keys));
    }

    /**
     * Return a copy of the array with the given `$keys` removed.
     *
     * @pure
     *
     * @template T of array
     * @template TKey as key-of<T>
     *
     * @param array $subject
     * @param non-empty-list<TKey> $keys
     *
     * @return T
     */
    public static function dropKeys(array $subject, array $keys): array
    {
        /**
         * Hack: Psalm doesn't yet support array negation via comment, hence
         * the slightly wider return type. Other variations of the @template
         * tags cause the happy path to raise PossiblyUndefined*ArrayOffset
         * warnings.
         *
         * @psalm-var T
         */
        return array_diff_key($subject, array_flip($keys));
    }

    /**
     * Group array elements based on the key returned by a callback function.
     *
     * Expects a function that takes a single argument (value from the
     * `$subject` array) and returns the desired array key.
     *
     * ```php
     * <?php
     *
     * use Clvarley\Utility\Arr;
     *
     * $grouped = Arr::group([1, 2, 3, 4], function (int $value) {
     *     return $value % 2 === 0 ? 'even' : 'odd';
     * });
     *
     * var_dump($grouped); // ['even' => [2, 4], 'odd' => [1, 3]]
     * ```
     *
     * @pure
     *
     * @template TKey of array-key
     * @template TVal
     *
     * @param TVal[] $subject
     * @param pure-callable(TVal): TKey $callback
     *
     * @psalm-return ($subject is non-empty-array ? non-empty-array<TKey, TVal[]> : array<TKey, TVal[]>)
     * @return array<TKey, TVal[]>
     */
    public static function group(array $subject, callable $callback): array
    {
        $groups = [];

        foreach ($subject as $value) {
            $key = $callback($value);

            if (!array_key_exists($key, $groups)) {
                $groups[$key] = [];
            }

            $groups[$key][] = $value;
        }

        return $groups;
    }

    /**
     * Join a series of array values into a natural language sentence.
     *
     * @pure
     *
     * @param array<scalar|Stringable> $subject
     * @param string $separator
     * @param string $conjunction
     *
     * @return ($subject is non-empty-array ? non-empty-string : string)
     */
    public static function naturalJoin(
        array $subject,
        string $separator = ', ',
        string $conjunction = ' and ',
    ): string {
        if (count($subject) <= 2) {
            return implode($conjunction, $subject);
        }

        /**
         * Hack: Psalm cannot determine the type assigned to $last. Even placing
         * an `assert(!empty($subject))` before this line has no effect.
         *
         * @psalm-var scalar|Stringable $last
         */
        $last = array_pop($subject);

        return implode($separator, $subject) . $conjunction . (string) $last;
    }
}
