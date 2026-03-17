<?php

namespace App\Domain\Calculator\Resource;

use App\Http\Resources\BaseResource;

class SubjectResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'required' => $this->required,
        ];
    }
}
