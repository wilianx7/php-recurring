<?php


namespace PhpRecurring\Exceptions;

use Exception;

class InvalidRepeatedCount extends Exception
{
    public function __construct()
    {
        parent::__construct("The repeated count is invalid. Must be a value greater than or equal zero.");
    }
}