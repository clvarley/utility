<?php declare(strict_types=1);

namespace Clvarley\Utility;

use Closure;
use TypeError;

use function assert;
use function call_user_func_array;
use function is_array;
use function is_object;
use function sprintf;

/**
 * @api
 */
final class Func
{
    /**
     * Creates a partial function from the provided callable and arguments.
     *
     * Because of PHP reference semantics you cannot partially apply functions
     * that have out parameters. This will effect usage of functions such as
     * `preg_match` where data is returned via the `&$matches` parameter.
     *
     * In those cases you have two options, use either the
     * {@see self::partialArray()} or {@see self::partialRef()} static methods:
     *
     * ```php
     * <?php
     *
     * use Clvarley\Utility\Func;
     *
     * $percentFinder = Func::partialArray('preg_match', ['/\d+%/']);
     * $percent = $percentFinder(['Save 12% in our sale', &$matches]);
     *
     * echo $matches[0]; // outputs -> 12%
     * ```
     *
     * @template T
     *
     * @param callable():T $callable
     * @param mixed[] $args
     *
     * @return callable(mixed...):T
     */
    public static function partial(callable $callable, mixed ...$args): callable
    {
        return static function (mixed ...$additionalArgs) use (
            $callable,
            $args
        ): mixed {
            return call_user_func_array($callable, [
                ...$args, ...$additionalArgs
            ]);
        };
    }

    /**
     * Creates a partial function where all arguments are passed by reference.
     *
     * This is only useful in a few limited scenarios, namely functions where
     * all the arguments are taken by reference or you don't expect to use
     * literal values.
     *
     * ```php
     * <?php
     *
     * use Clvarley\Utility\Func;
     *
     * $values = ['Jack', 'Jill', 'John'];
     *
     * $nameProvider = Func::partialRef('array_pop', $values);
     *
     * echo $nameProvider(); // outputs -> 'John'
     * echo $nameProvider(); // outputs -> 'Jill'
     * echo $nameProvider(); // outputs -> 'Jack'
     * ```
     *
     * Doing something like the following however will result in a PHP warning:
     *
     * ```php
     * <?php
     *
     * use Clvarley\Utility\Func;
     *
     * $findPrice = Func::partialRef('preg_match', '/[£$]\d+/');
     * $findPrice('Low price of $100', $matches); // warning -> $subject must be passed by reference
     * ```
     *
     * @template T
     *
     * @param callable():T $callable
     * @param mixed[] $args
     *
     * @return callable(mixed...):T
     */
    public static function partialRef(
        callable $callable,
        mixed &...$args
    ): callable {
        return static function (mixed &...$additionalArgs) use (
            $callable,
            $args
        ): mixed {
            return call_user_func_array($callable, [
                ...$args, ...$additionalArgs
            ]);
        };
    }

    /**
     * Creates a partial function from the provided callable and argument array.
     *
     * @template T
     *
     * @param callable():T $callable
     * @param mixed[] $args
     *
     * @return callable(array=):T
     */
    public static function partialArray(
        callable $callable,
        array $args = []
    ): callable {
        return static function (array $additionalArgs = []) use (
            $callable,
            $args
        ): mixed {
            return call_user_func_array($callable, [
                   ...$args, ...$additionalArgs
            ]);
        };
    }

    /**
     * Creates a bound function within the scope of `$object`.
     *
     * @template T
     *
     * @param callable():T $callable
     * @param object $object
     * @param mixed[] $args
     *
     * @throws TypeError If the given callable is a class method.
     *
     * @return callable(mixed...):T
     */
    public static function bind(
        callable $callable,
        object $object,
        mixed ...$args
    ): callable {
        self::validate($callable, 'bind');

        $callable = $callable->bindTo($object, $object);

        assert(null !== $callable);

        return static function (mixed ...$additionalArgs) use (
            $callable,
            $args
        ): mixed {
            return call_user_func_array($callable, [
                ...$args, ...$additionalArgs
            ]);
        };
    }

    /**
     * Calls a function within the scope of the provided `$object`.
     *
     * @template T
     *
     * @param callable():T $callable
     * @param object $object
     * @param mixed[] $args
     *
     * @throws TypeError
     *          If the given callable is a class method
     *
     * @return T
     */
    public static function apply(
        callable $callable,
        object $object,
        mixed ...$args
    ): mixed {
        self::validate($callable, 'apply');

        return $callable->call($object, ...$args);
    }

    /**
     * Validate that we have the correct type of callable.
     *
     * @psalm-assert Closure $callable
     *
     * @param callable $callable
     * @param 'bind'|'apply' $context
     *
     * @throws TypeError If the given callable is a class method.
     */
    private static function validate(
        callable $callable,
        string $context
    ): void {
        if ($callable instanceof Closure) {
            return;
        }

        if (is_array($callable)) {
            throw self::classMethodError($callable, $context);
        }

        if (is_object($callable)) {
            throw self::classMethodError([$callable, '__invoke'], $context);
        }

        throw self::notAClosureError($callable);
    }

    /**
     * Indicate that the supplied callable cannot be a class method.
     *
     * @param callable-array $method
     * @param 'bind'|'apply' $context
     *
     * @return TypeError
     */
    private static function classMethodError(
        callable $method,
        string $context
    ): TypeError {
        return new TypeError(sprintf(
            'Cannot %s method %s::%s() as methods are not allowed to be bound',
            $context,
            Debug::getClassName($method[0]),
            $method[1],
        ));
    }

    /**
     * Indicate that the supplied callable must be a closure.
     *
     * @param callable-string $function
     *
     * @return TypeError
     */
    private static function notAClosureError(callable $function): TypeError
    {
        return new TypeError(sprintf(
            'Cannot rebind scope of function %s() as it is not a closure',
            $function,
        ));
    }
}
