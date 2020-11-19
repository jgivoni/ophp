<?php
/**
 * @category Neo
 * @package ...
 * @copyright Vendo Services, GmbH
 */

namespace Ophp\Proton\Example;

use Ophp\Options;
use Ophp\Proton\AbstractStep;

class AuthorizeRequestCard extends AbstractStep
{
    public function __invoke(Options $options): ?Options
    {
        return new Options([
            'number' => $options->transaction->cardNumber,
            'cvv' => $options->transaction->cvv,
            'expiry' => $options->transaction->expiryMonth . '/' .
                substr($options->transaction->expiryYear, 0, 2),
        ]);
    }
}
