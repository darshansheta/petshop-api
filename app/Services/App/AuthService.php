<?php

namespace App\Services\App;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
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

