<?php

namespace PhpRecurring\Exceptions;

use Exception;

class InvalidFrequencyEndValue extends Exception
{
    public function __construct()
    {
        parent::__construct("The frequency end value is invalid");
    }
}
