<?php declare(strict_types=1);

namespace Tests\Unit\FuncTest;

use Clvarley\Utility\Debug;
use Clvarley\Utility\Func;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use TypeError;

#[CoversClass(Func::class)]
#[UsesClass(Debug::class)]
final class FuncTest extends TestCase
{
    public function testCanCreatePartiallyAppliedFunction(): void
    {
        $largestOver100 = Func::partial('max', 100);

        self::assertSame(100, $largestOver100(42));
        self::assertSame(120, $largestOver100(120));
        self::assertSame(200, $largestOver100(200));
        self::assertSame(300, $largestOver100(50, 100, 200, 300));
    }

    public function testCanCreatePartiallyAppliedFunctionUsingArray(): void
    {
        $findPercentage = Func::partialArray('preg_match', ['/\d+%/']);
        $findPercentage(['Save 12% in our summer sale', &$matches]);

        self::assertSame('12%', $matches[0]);
    }

    public function testCanCreateBoundFunction(): void
    {
        [$object1, $object2] = $this->provideExampleClasses();

        $greeter = fn (): string => 'Hello ' . $this->username;

        self::assertSame('Hello Test', Func::bind($greeter, $object1)());
        self::assertSame('Hello Example', Func::bind($greeter, $object2)());
    }

    public function testThrowsIfAttemptingToBindClassInstanceMethod(): void
    {
        [$object, ] = $this->provideExampleClasses();

        $greeter = new class () {
            public function greet(): string
            {
                return 'Hello ' . $this->username;
            }
        };

        self::expectException(TypeError::class);
        self::expectExceptionMessageMatches('/Cannot bind method .+::greet()/');

        Func::bind([$greeter, 'greet'], $object);
    }

    public function testThrowsIfAttemptingToBindClassStaticMethod(): void
    {
        [$object, ] = $this->provideExampleClasses();

        $greeter = new class () {
            public static function greet(): string
            {
                return 'Hi!';
            }
        };

        self::expectException(TypeError::class);
        self::expectExceptionMessageMatches('/Cannot bind method .+::greet()/');

        Func::bind([$greeter, 'greet'], $object);
    }

    public function testThrowsIfAttemptingToBindNonClosure(): void
    {
        [$object, ] = $this->provideExampleClasses();

        self::expectException(TypeError::class);
        self::expectExceptionMessageMatches('/Cannot rebind scope of function strlen/');

        Func::bind('strlen', $object);
    }

    public function testThrowsIfAttemptingToBindInvokableObject(): void
    {
        [$object, ] = $this->provideExampleClasses();

        $invokable = new class () {
            public function __invoke(): string
            {
                return $this->username;
            }
        };

        self::expectException(TypeError::class);
        self::expectExceptionMessageMatches('/Cannot bind method .+::__invoke()/');

        Func::bind($invokable, $object);
    }

    public function testCanApplyFunction(): void
    {
        [$object1, $object2] = $this->provideExampleClasses();

        $getter = fn (): string => $this->username;

        self::assertSame('Test', Func::apply($getter, $object1));
        self::assertSame('Example', Func::apply($getter, $object2));
    }

    public function testThrowsIfAttemptingToApplyClassInstanceMethod(): void
    {
        [$object, ] = $this->provideExampleClasses();

        $greeter = new class () {
            public function greet(): string
            {
                return 'Hello ' . $this->username;
            }
        };

        self::expectException(TypeError::class);
        self::expectExceptionMessageMatches('/Cannot apply method .+::greet()/');

        Func::apply([$greeter, 'greet'], $object);
    }

    public function testThrowsIfAttemptingToApplyClassStaticMethod(): void
    {
        [$object, ] = $this->provideExampleClasses();

        $greeter = new class () {
            public static function greet(): string
            {
                return 'Hi!';
            }
        };

        self::expectException(TypeError::class);
        self::expectExceptionMessageMatches('/Cannot apply method .+::greet()/');

        Func::apply([$greeter, 'greet'], $object);
    }

    public function testThrowsIfAttemptingToApplyNonClosure(): void
    {
        [$object, ] = $this->provideExampleClasses();

        self::expectException(TypeError::class);
        self::expectExceptionMessageMatches('/Cannot rebind scope of function strlen/');

        Func::apply('strlen', $object);
    }

    public function testThrowsIfAttemptingToApplyInvokableObject(): void
    {
        [$object, ] = $this->provideExampleClasses();

        $invokable = new class () {
            public function __invoke(): string
            {
                return $this->username;
            }
        };

        self::expectException(TypeError::class);
        self::expectExceptionMessageMatches('/Cannot apply method .+::__invoke()/');

        Func::apply($invokable, $object);
    }

    /**
     * @return array{0: object, 1: object}
     */
    private function provideExampleClasses(): array
    {
        return [
            new class () {
                private string $username = 'Test';
            },
            new class () {
                private string $username = 'Example';
            },
        ];
    }
}
