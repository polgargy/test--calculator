<?php

namespace App\Domain\Calculator\Resource;

use App\Http\Resources\BaseResource;

class InstitutionResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'faculties' => FacultyResource::collection($this->whenLoaded('faculties')),
        ];
    }
}
