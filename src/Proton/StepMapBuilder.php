<?php
/**
 * @category Neo
 * @package ...
 * @copyright Vendo Services, GmbH
 */

namespace Ophp\Proton;

class StepMapBuilder
{
    protected $map = [];

    /**
     * Adds a step by classname, step object or step map object
     *
     * @param $step
     * @return $this
     */
    public function addStep($step): self
    {
        $this->map[] = $step;
        return $this;
    }

    public function addSteps(array $steps): self
    {
        foreach ($steps as $step) {
            $this->addStep($step);
        }
        return $this;
    }

    public function getMap()
    {
        return new StepMap($this->map);
    }
}
