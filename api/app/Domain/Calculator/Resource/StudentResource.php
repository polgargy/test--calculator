<?php

namespace App\Domain\Calculator\Resource;

use App\Http\Resources\BaseResource;

class StudentResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'faculty_id' => $this->faculty_id,
            'base_points' => $this->base_points,
            'additional_points' => $this->additional_points,
            'total_points' => $this->base_points + $this->additional_points,
            'faculty' => new FacultyResource($this->whenLoaded('faculty')),
            'results' => ResultResource::collection($this->whenLoaded('results')),
            'language_exams' => LanguageExamResource::collection($this->whenLoaded('languageExams')),
            ...$this->timestamps(),
        ];
    }
}
