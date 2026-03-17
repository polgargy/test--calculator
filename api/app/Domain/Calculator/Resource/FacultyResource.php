<?php

namespace App\Domain\Calculator\Resource;

use App\Http\Resources\BaseResource;

class FacultyResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'institution_id' => $this->institution_id,
            'name' => $this->name,
            'required_subject_id' => $this->required_subject_id,
            'requires_advanced_level' => $this->requires_advanced_level,
            'required_subject' => new SubjectResource($this->whenLoaded('requiredSubject')),
            'elective_subjects' => SubjectResource::collection($this->whenLoaded('electiveSubjects')),
        ];
    }
}
