<?php

namespace App\Http\Controllers\API\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\App\AuthService;
use App\Http\Requests\API\V1\Admin\Auth\LoginRequest;

class AuthController extends Controller
{
    public function __construct(protected AuthService $authService) {}

    public function login(LoginRequest $request): Response
    {
        $token = $this->authService->login($request->only(['email', 'password']));

        if ($token) {
            return response([
                'token' => $token,
                'success' => 1,
            ]);
        } 

        return response([
            'success' => 0,
            'error' => "Failed to authenticate user"
        ], 401);
    }

    public function logout(): Response
    {
        $this->authService->logout();

        return response([
            'success' => 1,
            'message' => 'Token Revoked'
        ]);
    }
}
