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
class Validator extends Runner
{
    protected function onReturn()
    {
        return !array_search(false, $this->outputs);
    }
}
