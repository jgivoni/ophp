<?php
/**
 * @category Neo
 * @package ...
 * @copyright Vendo Services, GmbH
 */

namespace Ophp\Proton\Example;

class PaymentMethodIsCc extends TransactionCheckStep
{
    protected function check($transaction): bool
    {
        return $transaction->paymentMethod === 'CC';
    }
}
