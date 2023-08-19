<?php

namespace App\Services\Auth\Exceptions;

use Exception;
class MyJWTTokenMissing extends Exception
{
	protected $message = "token missing";

	public function render(Request $request): Response
	{
		return response([
			'error' => 'Unauthorized. '. $this->message,
		], 401);
	}
}
