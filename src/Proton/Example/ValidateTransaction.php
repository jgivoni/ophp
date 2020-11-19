<?php
/**
 * @category Neo
 * @package ...
 * @copyright Vendo Services, GmbH
 */

namespace Ophp\Proton\Example;

use Ophp\Options;
use Ophp\Proton\ProtonTrait;
use Ophp\Proton\StepMap;

class ValidateTransaction extends TransactionCheckStep
{
    use ProtonTrait;

    /**
     * @var \Ophp\Proton\StepMap
     */
    protected $stepMap;

    public function __construct(StepMap $stepMap)
    {
        $this->stepMap = $stepMap;
        return $this;
    }

    /**
     * @param $transaction
     * @return bool
     * @throws \Exception
     */
    protected function check($transaction): bool
    {
        $options = new Options([
            'transaction' => $transaction,
        ]);
        foreach ($this->stepMap as $step) {
            $step = $this->proton()->step($step);
            if (!$step instanceof TransactionCheckStep) {
                throw new \Exception('Invalid step type');
            }
            if (!$step($options)) {
                $isValid = false;
                break;
            }
        }
        return $isValid ?? true;
    }
}
