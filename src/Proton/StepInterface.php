<?php
/**
 * @category Ophp
 * @copyright Vendo Services, GmbH
 */

namespace Ophp\Proton;

use Ophp\Options;

interface StepInterface
{
    public function __invoke(Options $options): ?Options;
}
