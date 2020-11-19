<?php
/**
 * @category
 * @package
 * @copyright
 */

namespace Ophp\Proton2;

use PHPUnit\Framework\TestCase;

function getalife()
{
    return 'getalife';
}

class getalifeClass
{
    public function __invoke()
    {
        return 'getalife';
    }
}

class getalifeClass2
{
    public function foo()
    {
        return 'getalife';
    }
}

class getalifeClass3
{
    public static function bar()
    {
        return 'getalife';
    }
}

class RunnerTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testRunnerWithSingleCallable($callable)
    {
        $runner = runner($callable);
        $result = $runner();

        $this->assertEquals('getalife', $result);
    }

    public function dataProvider()
    {
        $getalife = 'getalife';

        /**
         * List of things that are callables
         */
        return [
            [__NAMESPACE__ . '\getalife'], // Named, namespaced function
            [function () {
                return 'getalife';
            }], // Anonymous function 1
            [function () use ($getalife) {
                return $getalife;
            }], // Closure with capture
            [fn() => 'getalife'], // Short (arrow) anonymous function 2
            [new getalifeClass], // Invokable object
            [fn() => (new getalifeClass2())->foo()], // Object method 1
            [
                [new getalifeClass2(), 'foo']
            ], // Object method 2
            [getalifeClass3::class . '::bar'], // Static method 1
            [[getalifeClass3::class, 'bar']], // Static method 2
        ];
    }
}
