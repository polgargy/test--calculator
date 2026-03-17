<?php

namespace App\Domain\Calculator\Repository;

use App\Domain\Calculator\Models\Institution;
use Illuminate\Database\Eloquent\Collection;

class InstitutionRepository
{
    public function getAll(): Collection
    {
        return Institution::with([
            'faculties.requiredSubject',
            'faculties.electiveSubjects',
        ])->get();
    }
}
