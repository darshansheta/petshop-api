<?php

namespace App\Services\App;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Auth;

class AdminService
{
    public function createAdmin (array $data): User
    {
        $user = new User;

        $user->is_admin = 0;
        $user->first_name = $data['first_name'];
        $user->last_name = $data['last_name'];
        $user->email = $data['email'];
        $user->password = $data['password'];
        $user->avatar = $data['avatar'] ?? null;
        $user->address = $data['address'];
        $user->phone_number = $data['phone_number'];
        $user->is_marketing = empty($data['marketing']) ? 0 : 1;

        $user->save();

        return $user;
    }
}