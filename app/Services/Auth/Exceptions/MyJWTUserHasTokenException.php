<?php

namespace App\Services\Auth\Exceptions;

use Exception;
class MyJWTUserHasTokenException extends Exception
{
	protected $message = "User has token";
}
