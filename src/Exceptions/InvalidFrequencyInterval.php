<?php


namespace PhpRecurring\Exceptions;

use Exception;

class InvalidFrequencyInterval extends Exception
{
    public function __construct()
    {
        parent::__construct("The frequency interval is invalid. Must be a value greater than zero.");
    }
}