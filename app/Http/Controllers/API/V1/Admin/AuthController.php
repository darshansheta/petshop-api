<?php

namespace App\Http\Controllers\API\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use App\Services\App\AuthService;
use App\Http\Requests\API\V1\Admin\Auth\LoginRequest;

class AuthController extends Controller
{
    public function __construct(protected AuthService $authService)
    {
    }

    /**
     * @OA\Post(
     *      path="/admin/login",
     *      operationId="doLogin",
     *      tags={"Auth"},
     *      summary="Login admin user",
     *      description="Login admin and return jwt token",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     description="User email"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     description="User password"
     *                 ),
     *                 required={"email","payment_uuid","password"}
     *             )
     *         )
     *     ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful login",
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Failed to authenticate",
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unauthenticated",
     *      )
     *     )
     */
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
            'error' => 'Failed to authenticate user',
        ], 401);
    }

    /**
     * @OA\Delete(
     *      path="/admin/logout",
     *      operationId="doLogout",
     *      tags={"Auth"},
     *      summary="Logout admin user",
     *      description="Logout admin and return revoke token",
     *      security={{ "apiAuth": {} }},
     *      @OA\Response(
     *          response=200,
     *          description="Successful logout",
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Failed to authenticate",
     *      )
     *     )
     */
    public function logout(): Response
    {
        $this->authService->logout();

        return response([
            'success' => 1,
            'message' => 'Token Revoked',
        ]);
    }
}
