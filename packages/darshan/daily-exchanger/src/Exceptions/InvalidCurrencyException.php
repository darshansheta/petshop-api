<?php

namespace Darshan\DailyExchanger\Exceptions;

use Exception;

class InvalidCurrencyException extends Exception
{

    public function __construct($message = 'Invalid currency')
    {
        parent::__construct($message);
    }

}
