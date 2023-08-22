<?php

namespace App\Services\Auth\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MyJWTUserHasTokenException extends Exception
{
    protected $message = 'User has token';

    public function render(Request $request): Response
    {
        return response([
            'error' => 'Unauthorized, ' . $this->message,
        ], 401);
    }
}
