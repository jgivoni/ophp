<?php
/**
 * @category Neo
 * @package ...
 * @copyright Vendo Services, GmbH
 */

namespace Ophp\Proton;

use Ophp\Exception;
use Ophp\Options;

class Manager
{
    public function stepThrough(array $steps): void
    {
        $this->getResult(null, $steps);
    }

    public function walk(StepMap $stepMap)
    {

    }

    public function getResult(?Options $value = null, array $steps = []): ?Options
    {
        foreach ($steps as $stepClass) {
            $step = new $stepClass;
            $value = $step($value);
        }
        return $value;
    }

    public function process(?Options $value = null, array $steps = []): void
    {
        $this->getResult($value, $steps);
    }

    public function stepMapBuilder()
    {
        return new StepMapBuilder();
    }

    /**
     * @param string|array|\Ophp\Proton\AbstractStep|callable $step
     * @return callable
     */
    public function step($step): callable
    {
        if (is_string($step)) {
            $step = new $step;
        } elseif (is_array($step)) {
            $stepClass = array_pop($step);
            $step = new $stepClass(...$step);
        }
        return $step;
    }
}
