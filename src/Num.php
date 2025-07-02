<?php declare(strict_types=1);

namespace Clvarley\Utility;

use function fmod;
use function is_int;

/**
 * @api
 */
final class Num
{
    /**
     * Determine if the given number has a fractional/decimal component.
     *
     * @pure
     *
     * @param int|float $value
     *
     * @psalm-return ($value is int ? false : bool)
     * @return bool
     */
    public static function hasFractional(int|float $value): bool
    {
        return 0.0 !== self::getFractional($value);
    }

    /**
     * Return the fractional component of a number.
     *
     * In essence, returns all the digits after the decimal place.
     *
     * @pure
     *
     * @param int|float $value
     *
     * @psalm-return ($value is int ? 0.0 : float)
     * @return float
     */
    public static function getFractional(int|float $value): float
    {
        if (is_int($value)) {
            return 0.0;
        }

        return fmod($value, 1.0);
    }
}
