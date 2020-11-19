<?php
/**
 * @category
 * @package
 * @copyright
 */

namespace Ophp\Proton2;

/**
 * Executes all callables with the result of one as the input to the next
 */
class Pipeline extends Runner
{

    protected function onExecute($input)
    {
        $this->inputs[0] = $input;
        $this->outputs = array_fill(0, count($this->callables), null);

        for ($i = 1; $i < count($this->callables); $i++) {
            $this->inputs[$i] = [&$this->outputs[$i - 1]];
        }
    }
}
