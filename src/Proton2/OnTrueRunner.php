<?php
/**
 * @category
 * @package
 * @copyright
 */

namespace Ophp\Proton2;

class OnTrueRunner extends Runner
{
    public function __construct(...$callables)
    {
        $newCallables = [];
        foreach ($callables as $i => $callable) {
            $newCallables[] = function ($result) use ($callable) {
                if ($result) {
                    call_user_func($callable, [$result]);
                }
            };
        }

        parent::__construct($newCallables);
    }
}

function onTrue(...$callables)
{
    return new OnTrueRunner(...$callables);
}
