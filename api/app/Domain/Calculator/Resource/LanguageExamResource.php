<?php

namespace App\Domain\Calculator\Resource;

use App\Http\Resources\BaseResource;

class LanguageExamResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'language' => $this->language,
            'level' => $this->level,
        ];
    }
}
