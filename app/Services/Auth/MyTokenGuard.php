<?php

namespace App\Services\Auth;

use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Services\Auth\MyJWT;

class MyTokenGuard implements Guard
{
	use GuardHelpers;

    public function __construct(MyJWT $jwt, UserProvider $provider, Request $request)
    {
        $this->jwt = $jwt;
        $this->provider = $provider;
        $this->request = $request;
    }

    public function getToken(): string
    {
    	return $this->request->bearerToken();
    }

}
