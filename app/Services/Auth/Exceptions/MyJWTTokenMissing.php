<?php

namespace App\Services\Auth\Exceptions;

use Exception;
class MyJWTTokenMissing extends Exception
{
	protected $message = "token missing";
}
