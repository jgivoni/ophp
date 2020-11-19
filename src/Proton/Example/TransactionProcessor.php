<?php
/**
 * @category Neo
 * @package ...
 * @copyright Vendo Services, GmbH
 */

namespace Ophp\Proton;

use Ophp\Proton\Example\AuthorizeRequest;
use Ophp\Proton\Example\TransactionIsSuccessful;
use Ophp\Proton\Example\ValidateTransaction;

class TransactionProcessor
{
    use ProtonTrait;

    public function authorizeTransaction($transaction)
    {
        $this->proton()->stepThrough([
            ValidateTransaction::class,
            AuthorizeRequest::class,
            FilterRequest::class,
            GetResponse::class,
            ParseResponse::class,
        ]);

        return $this->proton()->stepMapBuilder()
            ->addStep([ValidateTransaction::class, [
                TransactionIsSuccessful::class,
            ]])
            ->addStep(AuthorizeRequest::class)
            ->addStep(FilterRequest::class)
            ->addStep(GetResponse::class)
            ->addStep(ParseResponse::class)
            ->addStep(GetResult::class)
            ->getMap()($transaction);
    }
}
