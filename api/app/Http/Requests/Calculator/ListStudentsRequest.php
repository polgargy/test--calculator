<?php

namespace App\Http\Requests\Calculator;

use App\Http\Requests\PaginatedRequest;

class ListStudentsRequest extends PaginatedRequest
{
    protected function allowedOrderByColumns(): array
    {
        return ['id', 'name', 'base_points', 'additional_points', 'created_at', 'updated_at'];
    }
}
