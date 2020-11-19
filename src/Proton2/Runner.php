<?php
/**
 * @category
 * @package
 * @copyright
 */

namespace Ophp\Proton2;

use Ophp\Exception;

/**
 * Executes all callables with the same input
 * Returns a list of the results
 */
class Runner
{
    /**
     * @var callable[]
     */
    protected array $callables;

    public function __construct(...$callables)
    {
        $this->callables = $callables;
    }

    public function execute(...$params)
    {
        $result = [];
        foreach ($this->callables as $callable) {
            try {
                $result[] = call_user_func($callable, ...$params);
            } catch (\Exception $e) {
                $result[] = new ExceptionResult($e);
            }
        }

        return $result;
    }

    public function __invoke(...$params)
    {
        return $this->execute(...$params);
    }
}

function runner(...$callables)
{
    return new Runner(...$callables);
}
