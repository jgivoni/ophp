<?php
/**
 * @category
 * @package
 * @copyright
 */

namespace Ophp\subway;

/**
 * Sets a timeout on a function call
 */
class SubwayTimeout
{
    public static function callWithTimeout(int $milliseconds,
                                           callable $function,
                                           ?callable $onTimeout = null)
    {
        try {
            $startTime = microtime(true);
            $tickFunction = function () use ($startTime, $milliseconds) {
                if ((microtime(true) - $startTime) > $milliseconds * 1000) {
                    throw new TimeoutException();
                }
            };
            register_tick_function($tickFunction);
            $result = $function();
        } catch (TimeoutException $e) {
            if (isset($onTimeout)) {
                $result = $onTimeout();
            }
        } finally {
            unregister_tick_function($tickFunction);
        }
        return $result ?? null;
    }

    public function example()
    {
        // Setting up scope vars
        $arg1 = 9;
        $arg2 = 'getalife';

        // Calling the function normally
        $result = calculate_something_complicated($arg1, $arg2);

        // Call it with a timeout
        $result = SubwayTimeout::callWithTimeout(1000,
            fn() => calculate_something_complicated($arg1, $arg2),
        );
        // Continue - $result might be null

        // Call it with special code on timeout
        $result = SubwayTimeout::callWithTimeout(1000,
            fn() => calculate_something_complicated($arg1, $arg2),
            function () {
                trigger_error('Calculation timed out!', E_USER_WARNING);
                return null; // Or throw?
            }
        );
    }
}
