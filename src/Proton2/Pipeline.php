<?php
/**
 * @category
 * @package
 * @copyright
 */

namespace Ophp\Proton2;

use Ophp\Exception;

/**
 * Executes all callables with the result of one as the input to the next
 */
class Pipeline extends Runner
{
    public function execute(...$params)
    {
        foreach ($this->callables as $callable) {
            try {
                $result = call_user_func($callable, ...$params);
            } catch (Exception $e) {
                $result = new ExceptionResult($e);
            }
            $params = [$result];
        }

        if ($result instanceof ExceptionResult) {
            throw $result->getException();
        }

        return $result;
    }
}

function pipeline(...$callables)
{
    return new Pipeline(...$callables);
}
