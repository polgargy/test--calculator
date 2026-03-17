<?php

namespace App\Domain\Calculator\Resource;

use App\Http\Resources\BaseResource;

class ResultResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'subject_id' => $this->subject_id,
            'advanced_level' => $this->advanced_level,
            'result' => $this->result,
            'subject' => new SubjectResource($this->whenLoaded('subject')),
        ];
    }
}
