<?php

namespace App\Services\App;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class AdminUserService
{
    protected array $appplicableFilters = [
        'first_name' => ['column' => 'users.first_name', 'type' => 'like'],
        'last_name' => ['column' => 'users.last_name', 'type' => 'like'],
        'email' => ['column' => 'users.email', 'type' => 'like'],
        'phone' => ['column' => 'users.phone_number', 'type' => 'like'],
        'address' => ['column' => 'users.address', 'type' => 'like'],
        'created_at' => ['column' => 'users.created_at', 'type' => 'date'],
        'is_marketing' => ['column' => 'users.is_marketing', 'type' => 'boolean'],
    ];

    protected array $sortableColumns = [
        'users.first_name',
        'users.last_name',
        'users.email',
        'users.phone_number',
        'users.address',
        'users.created_at',
        'users.is_marketing',
    ];

    public function getListing(
        array $pagination = ['perPage' => 15, 'page' => 1],
        array $orderBy = ['sortBy' => 'created_at', 'sortOrder'=> 'desc'],
        array $filters = [],
    ): LengthAwarePaginator {
        $query = User::query()
                ->customer();

        $query = $this->applyFilters($query, $filters);

        if (in_array($orderBy['sortBy'], $this->sortableColumns)) {
            $query->orderBy($orderBy['sortBy'], $orderBy['sortOrder']);
        }

        return $query
             ->paginate($pagination['perPage'], [
                 'users.id',
                 'users.uuid',
                 'users.first_name',
                 'users.last_name',
                 'users.email',
                 'users.address',
                 'users.phone_number',
                 'users.is_marketing',
                 'users.created_at',
                 'users.updated_at',
             ], 'page', $pagination['page']);
    }

    protected function applyFilters(Builder $query, array $inputFilters): Builder
    {
        foreach ($this->appplicableFilters as $filterKey => $filter) {
            if (empty($inputFilters[$filterKey])) {
                continue;
            }

            switch ($filter['type']) {
                case 'like':
                    $query->where($filter['column'], 'like', '%' . $inputFilters[$filterKey] . '%');
                    break;

                case 'date':
                    if (strtotime($inputFilters[$filterKey])) {
                        $filterDate = Carbon::parse($inputFilters[$filterKey]);
                        $query->whereBetween($filter['column'], [
                            $filterDate->startOfDay(),
                            $filterDate->endOfDay(),
                        ]);
                    }
                    break;

                case 'boolean':
                    if (isset($inputFilters[$filterKey])) {
                        $query->where($filter['column'], $inputFilters[$filterKey]);
                    }
                    break;

                default:
                    if (isset($inputFilters[$filterKey])) {
                        $query->where($filter['column'], $inputFilters[$filterKey]);
                    }

                    break;
            }
        }

        return $query;
    }

    public function updateUser(User $user, array $data): User
    {
        $user->first_name = $data['first_name'];
        $user->last_name = $data['last_name'];
        $user->email = $data['email'];
        $user->password = $data['password'];
        $user->avatar = $data['avatar'] ?? null;
        $user->address = $data['address'];
        $user->phone_number = $data['phone_number'];
        $user->is_marketing = empty($data['is_marketing']) ? 0 : 1;

        $user->save();

        return $user;
    }

    public function deleteUser(User $user): void
    {
        // Do pre delete process

        // Delete user
        $user->delete();

        //Do post delete process
    }
}
