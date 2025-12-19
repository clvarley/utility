<?php declare(strict_types=1);

namespace Clvarley\Utility;

use function assert;
use function error_get_last;
use function error_reporting;

/**
 * @api
 *
 * @psalm-type Error = array{type: int, message: string, file: string, line: int}
 */
final class Error
{
    /**
     * @template T
     *
     * @param callable():T $callback
     *
     * @return T
     */
    public static function suppress(callable $callback): mixed
    {
        $current = error_reporting(0);

        try {
            $result = $callback();
        } finally {
            error_reporting($current);
        }

        return $result;
    }

    /**
     * @template T
     *
     * @param callable():T $callback
     *
     * @return array{0: T, 1: null}|array{0: T|null, 1: Error}
     */
    public static function suppressResult(callable $callback): array
    {
        $previousError = error_get_last();
        $result = self::suppress($callback);
        $currentError = error_get_last();

        if ($currentError !== $previousError) {
            assert(null !== $currentError);

            return [$result, $currentError];
        }

        return [$result, null];
    }
}
