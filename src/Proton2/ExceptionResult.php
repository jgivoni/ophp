<?php
/**
 * @category
 * @package
 * @copyright
 */

namespace Ophp\Proton2;

class ExceptionResult
{
    protected \Exception $exception;

    public function __construct(\Exception $exception)
    {
        $this->exception = $exception;
    }

    public function getException()
    {
        return $this->exception;
    }
}