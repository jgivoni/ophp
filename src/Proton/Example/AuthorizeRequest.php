<?php
/**
 * @category Neo
 * @package ...
 * @copyright Vendo Services, GmbH
 */

namespace Ophp\Proton\Example;

use Ophp\Options;
use Ophp\Proton\AbstractStep;
use Ophp\Proton\ProtonTrait;

class AuthorizeRequest extends AbstractStep
{
    use ProtonTrait;

    public function __invoke(Options $options): ?Options
    {
        return new Options([
            'accountId' => $options->processorAccount->id,
            'amount' => $options->transaction->amount,
            'card' => $this->getCard($options),
        ]);
    }

    protected function getCard(Options $options): ?Options
    {
        return $this->proton()->stepThrough([
            AuthorizeRequestCard::class,
        ]);
    }
}
