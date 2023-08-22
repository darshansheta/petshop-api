<?php

namespace App\Services\App;

use Auth;

class AuthService
{
    public function login(array $data): ?string
    {
        return Auth::guard('jwt')->attempt($data);
    }

    public function logout(): void
    {
        Auth::guard('jwt')->logout();
    }
}
