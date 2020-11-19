<?php

namespace Ophp\Proton2;

function runner(...$callables)
{
    return new Runner(...$callables);
}

function pipeline(...$callables)
{
    return new Pipeline(...$callables);
}

function onTrue(...$callables)
{
    return new OnTrueRunner(...$callables);
}

function parallel(...$callables)
{
    return new Parallel(...$callables);
}

function throwExceptionOnFalse(...$callables)
{
    return new ThrowExceptionOnFalseRunner(...$callables);
}

function validator(...$callables)
{
    return new Validator(...$callables);
}
