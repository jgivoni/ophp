<?php
/**
 * @category Neo
 * @package ...
 * @copyright Vendo Services, GmbH
 */

namespace Ophp\Proton\Example;

class TransactionIsSuccessful extends TransactionCheckStep
{
    protected function check($transaction): bool
    {
        return $transaction->status != false;
    }
}
