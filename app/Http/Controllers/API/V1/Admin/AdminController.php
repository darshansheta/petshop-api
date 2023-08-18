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

    public function store(AdminStoreRequest $request): JsonResource
    {
        $admin = $this->adminService->createAdmin($request->validated());

        return (new AdminResource($admin));
    }
}
