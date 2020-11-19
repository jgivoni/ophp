<?php
/**
 * @category
 * @package
 * @copyright
 */

namespace Ophp\Proton2;

/**
 * A validator runs all its checks (optionally only until one fails) and returns a boolean - true if all pass
 * Each check is independent from the rest so they can potentially be run in parallel
 */
class Validator extends Pipeline
{
    /**
     * @var bool
     */
    protected $result = true;

    public function __construct(...$callables)
    {
        $newCallables = [];

        foreach ($callables as $i => $callable) {
            $newCallables[] = function ($input) use ($callable) {
                $this->result = $this->result && call_user_func($callable, $input);
                return $input;
            };
        }

        $newCallables[] = fn() => $this->result;

        parent::__construct(...$newCallables);
    }
}
