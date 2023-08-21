<?php

namespace App\Http\Controllers\API\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\API\V1\Admin\AdminResource;
use App\Services\App\AdminService;
use App\Http\Requests\API\V1\Admin\AdminStoreRequest;

class AdminController extends Controller
{
    public function __construct(protected AdminService $adminService) {}
    /**
     * @OA\Post(
     *      path="/admin/create",
     *      operationId="createAdmin",
     *      tags={"Admin"},
     *      summary="Create an admin user",
     *      security={{ "apiAuth": {} }},
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="first_name",
     *                     type="string",
     *                     description="User first_name"
     *                 ),
     *                 @OA\Property(
     *                     property="last_name",
     *                     type="string",
     *                     description="User last_name"
     *                 ),
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
     *                 @OA\Property(
     *                     property="password_confirmation",
     *                     type="string",
     *                     description="User password_confirmation"
     *                 ),
     *                 @OA\Property(
     *                     property="address",
     *                     type="string",
     *                     description="User address"
     *                 ),
     *                 @OA\Property(
     *                     property="phone_number",
     *                     type="string",
     *                     description="User phone_number"
     *                 ),
     *                 @OA\Property(
     *                     property="avatar",
     *                     type="string",
     *                     description="User avatar"
     *                 ),
     *                 @OA\Parameter(
     *                     name="marketing",
     *                     in="query",
     *                     required=false,
     *                 @OA\Schema(
     *                     type="string",
     *                     enum={"0", "1"},
     *                     description="Select an option",
     *                   ),
     *     ),
     *                 required={"email","first_name","last_name","password","password_confirmation","address","phone_number"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="OK"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Page not found"
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Unprocessable Entity"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Internal server error"
     *     )
     *     )
     */
    public function store(AdminStoreRequest $request): JsonResource
    {
        $admin = $this->adminService->createAdmin($request->validated());

        return (new AdminResource($admin));
    }
}
