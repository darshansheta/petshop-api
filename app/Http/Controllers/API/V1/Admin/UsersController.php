<?php

namespace App\Http\Controllers\API\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\App\AdminUserService;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\API\V1\Admin\UserListCollection;

class UsersController extends Controller
{
    public function __construct(protected AdminUserService $adminUserService) {}

    public function index(Request $request)
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
}
