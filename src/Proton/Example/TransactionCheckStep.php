<?php
/**
 * @category Neo
 * @package ...
 * @copyright Vendo Services, GmbH
 */

namespace Ophp\Proton\Example;

use Ophp\Options;
use Ophp\Proton\BooleanStep;

abstract class TransactionCheckStep extends BooleanStep
{
    protected function getBool(Options $options): bool
    {
        return $this->check($options->transaction);
    }

    abstract protected function check($transaction): bool;
}
