<?php
/**
 * @category Ophp
 * @copyright Jakob Givoni 2020
 */

namespace Ophp\Proton;

use Ophp\Options;

abstract class AbstractStep implements StepInterface
{
    abstract public function __invoke(Options $options): ?Options;
}
