<?php
/**
 * @category
 * @package
 * @copyright
 */

namespace Ophp\Proton2;

/**
 * Executes all callables with the same input
 * Returns the last result
 */
class Runner
{
    /**
     * @var callable[]
     */
    protected array $callables = [];

    /**
     * @var array
     */
    protected array $inputs = [];

    /**
     * @var array
     */
    protected array $outputs = [];

    public function __construct(...$callables)
    {
        $this->setCallables($callables);
    }

    protected function setCallables(array $callables): void
    {
        $this->callables = $callables;
    }

    protected function addCallable(callable $callable): void
    {
        $this->callables[] = $callable;
    }

    protected function onExecute($input)
    {
        $this->inputs = array_fill(0, count($this->callables), $input);
    }

    public function execute(...$params)
    {
        $this->onExecute($params);

        foreach ($this->callables as $i => $callable) {
            try {
                $input = $this->inputs[$i];
                $output = call_user_func($callable, ...$input);
            } catch (\Exception $e) {
                $output = new ExceptionResult($e);
            }
            $this->outputs[$i] = $output;
        }

        return $this->onReturn();
    }

    /**
     * @return mixed
     */
    protected function onReturn()
    {
        return end($this->outputs);
    }

    public function __invoke(...$params)
    {
        return $this->execute(...$params);
    }

    public static function runner(...$callables)
    {
        return new Runner(...$callables);
    }

    public static function pipeline(...$callables)
    {
        return new Pipeline(...$callables);
    }

    public static function validator(...$callables)
    {
        return new Validator(...$callables);
    }

    public static function onTrue(...$callables)
    {
        return new OnTrueRunner(...$callables);
    }

    public static function parallel(...$callables)
    {
        return new Parallel(...$callables);
    }

    public static function throwExceptionOnFalse(...$callables)
    {
        return new ThrowExceptionOnFalseRunner(...$callables);
    }
}
