<?php

namespace App\Services\Auth\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MYJWTTokenExpired extends Exception
{
	protected $message = "token expired";

	public function render(Request $request): Response
	{
		return response([
			'error' => 'Unauthorized, '. $this->message,
		], 401);
	}
}
