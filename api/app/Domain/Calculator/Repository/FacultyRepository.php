<?php

namespace App\Domain\Calculator\Repository;

use App\Domain\Calculator\Models\Faculty;

class FacultyRepository
{
    public function findWithElectives(int $id): ?Faculty
    {
        return Faculty::with('electiveSubjects')->find($id);
    }
}
