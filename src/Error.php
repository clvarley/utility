<?php declare(strict_types=1);

namespace Clvarley\Utility;

use function error_reporting;

/**
 * @api
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
}
