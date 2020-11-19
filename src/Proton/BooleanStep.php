<?php
/**
 * @category Neo
 * @package ...
 * @copyright Vendo Services, GmbH
 */

namespace Ophp\Proton;

use Ophp\Options;

abstract class BooleanStep extends AbstractStep
{
    public function __invoke(Options $options): ?Options
    {
        return new Options([
            'result' => $this->getBool($options),
        ]);
    }

    abstract protected function getBool(Options $options): bool;
}
