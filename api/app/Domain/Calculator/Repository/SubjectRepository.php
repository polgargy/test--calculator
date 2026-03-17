<?php

namespace App\Domain\Calculator\Repository;

use App\Domain\Calculator\Models\Subject;
use Illuminate\Database\Eloquent\Collection;

class SubjectRepository
{
    public function getRequired(): Collection
    {
        return Subject::where('required', true)->orderBy('id')->get();
    }
}
