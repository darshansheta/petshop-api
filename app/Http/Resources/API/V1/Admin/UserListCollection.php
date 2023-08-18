<?php

namespace App\Http\Resources\API\V1\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserListCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return ['data' => $this->collection];
    }
}
