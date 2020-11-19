<?php
/**
 * @category
 * @package
 * @copyright
 */

function equals($a, $b)
{
    if (!is_scalar($a) || !is_scalar($b)) {
        throw new Exception('The arguments must be scalar!');
    }
    return $a == $b;
}

// Simple call
$result = equals(1, 2); // False
$result = equals(1, 1); // True
$result = equals(1, null); // Exception

// Proton2 call
$operation = new Ophp\Proton2\Runner('equals');
$result = $operation(1, 2);

// Decorate call with shim so we can call with alternative arguments

function equalsShim(array $aAndB): \Ophp\Proton2\Arguments
{
    return new \Ophp\Proton2\Arguments($aAndB['left'] ?? null, $aAndB['right'] ?? null);
}

$myEqualsOperation = new class extends \Ophp\Proton2\Runner {
    public function __construct()
    {
        parent::__construct('equals');
    }

    public function execute(...$params)
    {
        $params = equalsShim(...$params)->getArgumentsAsArray();
        try {
            $result = parent::execute(...$params);
        } catch (Exception $e) {

        }
    }
};

$result = $myEqualsOperation(['left' => 1, 'right' => 2], true); // "Unequal"
$result = $myEqualsOperation(['left' => 1, 'right' => 1], true); // "Equal"
$result = $myEqualsOperation(['left' => 1, 'right' => null], true); // null
