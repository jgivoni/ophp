<?php

namespace MyApp;

use function Ophp\Proton2\pipeline;
use function Ophp\Proton2\runner;
use function Ophp\Proton2\validator;

/**
 * Operations are executed in order no matter what
 * Output from one operation is input to the next
 * Primitive wrapper functions can 'alter' the behavior, f.ex.
 *      onTrue() only executes its operation if the result of the previous one was true
 *      onException() only executes its operation if the previous operation threw an exception
 *      throwExceptionOnFalse() throws an exception if its operation returns false
 */
class Transaction
{

}

$transaction = new Transaction();

$result = runner(
    validator(
        'transactionIsProcessing',
        'transactionCaptureStatusIsNotSet'
    ),
    pipeline(
        'createRequest',
        'loadResponse',
        'parseResponse',
    )
)
($transaction);
