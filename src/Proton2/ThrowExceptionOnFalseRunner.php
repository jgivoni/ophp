<?php
/**
 * @category
 * @package
 * @copyright
 */

namespace Ophp\Proton2;

class ThrowExceptionOnFalseRunner extends Runner
{
    public function __construct(...$callables)
    {
        $callables[] = function ($result) {
            if (!$result) {
                throw new \Exception('Result is false');
            }
        };

        parent::__construct($callables);
    }
}

function throwExceptionOnFalse(...$callables)
{
    return new ThrowExceptionOnFalseRunner(...$callables);
}
