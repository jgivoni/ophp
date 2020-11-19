<?php
/**
 * @category Neo
 * @package ...
 * @copyright Vendo Services, GmbH
 */

namespace Ophp\Proton;

trait ProtonTrait
{
    /**
     * @var \Ophp\Proton\Manager
     */
    protected $proton;

    protected function proton(): Manager
    {
    }
}
