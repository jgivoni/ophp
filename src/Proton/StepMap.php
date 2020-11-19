<?php
/**
 * @category Neo
 * @package ...
 * @copyright Vendo Services, GmbH
 */

namespace Ophp\Proton;

class StepMap implements \Iterator
{
    /**
     * @var array
     */
    protected $map;

    public function __construct(array $map)
    {
        $this->map = $map;
    }

    public function current()
    {
        return current($this->map);
    }

    public function key()
    {
        return null;
    }

    public function next(): void
    {
        next($this->map);
    }

    public function rewind(): void
    {
        reset($this->map);
    }

    public function valid(): bool
    {
        return key($this->map) !== null;
    }
}
