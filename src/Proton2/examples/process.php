<?php

/**
 * Operations are executed in order no matter what
 * Output from one operation is input to the next
 * Primitive wrapper functions can 'alter' the behavior, f.ex.
 *      onTrue() only executes its operation if the result of the previous one was true
 *      onException() only executes its operation if the previous operation threw an exception
 *      throwExceptionOnFalse() throws an exception if its operation returns false
 */

$validateTransaction = \Ophp\Proton2\runner(
    \Ophp\Proton2\throwExceptionOnFalse(
        'transactionIsProcessing',
        \Ophp\Proton2\onTrue('transactionCaptureStatusIsNotSet')
    )
);

$operation = new \Ophp\Proton2\Runner(
    $validateTransaction,
    'createRequest',
    'getResponse',
    'parseResponse',
);

$result = $operation($transaction);
