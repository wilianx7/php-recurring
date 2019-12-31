<?php


namespace PhpRecurring\Exceptions;

use Exception;

class InvalidRepeatIn extends Exception
{
    public function __construct()
    {
        parent::__construct("The repeat in is invalid");
    }
}