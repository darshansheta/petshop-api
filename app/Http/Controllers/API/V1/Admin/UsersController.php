<?php

namespace App\Http\Controllers\API\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\App\AdminUserService;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\API\V1\Admin\UserListCollection;
use App\Http\Resources\API\V1\Admin\UserListResource;
use App\Http\Requests\API\V1\Admin\UserUpdateRequest;
use App\Models\User;

class UsersController extends Controller
{
    public function __construct(protected AdminUserService $adminUserService) {}

    public function index(Request $request): UserListCollection
    {
        $pagination = [
            'page' => $request->query('page', 1),
            'perPage' => $request->query('limit', 15),
        ];

        $orderBy = [
            'sortBy' => $request->query('sortBy', 'users.created_at'),
            'sortOrder' => $request->has('desc') ? 'desc' : 'asc',
        ];

        $result = $this->adminUserService->getListing($pagination, $orderBy, $request->all());
        
        return new UserListCollection($result);
    }

    public function update(User $user, UserUpdateRequest $request): UserListResource
    {
        $user = $this->adminUserService->updateUser($user, $request->validated());
        return new UserListResource($user);
    }

    public function delete(User $user): Response
    {
        $this->adminUserService->deleteUser($user);
        return response([
            'success' => 1,
            'message' => 'User deleted',
        ]);
    }
}
