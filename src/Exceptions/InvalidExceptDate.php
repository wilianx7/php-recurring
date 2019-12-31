<?php


namespace PhpRecurring\Exceptions;

use Exception;

class InvalidExceptDate extends Exception
{
    public function __construct()
    {
        parent::__construct("The except date is invalid. Must be a instance of carbon.");
    }
}