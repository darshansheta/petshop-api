<?php

namespace Darshan\DailyExchanger\Exceptions;

use Exception;

class InvalidAmountException extends Exception
{

    public function __construct($message = 'Invalid amount')
    {
        parent::__construct($message);
    }
}
