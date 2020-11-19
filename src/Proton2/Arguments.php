<?php
/**
 * @category
 * @package
 * @copyright
 */

namespace Ophp\Proton2;

class Arguments
{
    protected array $arguments;

    public function __construct(...$arguments)
    {
        $this->arguments = $arguments;
    }

    public function getArgumentsAsArray()
    {
        return $this->arguments;
    }
}