<?php

namespace App\Domain\Calculator\Repository;

use App\Domain\Calculator\Models\LanguageExam;
use App\Domain\Calculator\Models\Result;
use App\Domain\Calculator\Models\Student;
use App\Traits\Paginatable;
use Illuminate\Database\Eloquent\Builder;

class StudentRepository
{
    use Paginatable;

    protected function buildQuery(array $filters = []): Builder
    {
        return Student::with([
            'faculty.institution',
            'results.subject',
            'languageExams',
        ]);
    }

    public function create(array $data): Student
    {
        return Student::create($data);
    }

    public function createResult(int $studentId, array $data): Result
    {
        return Result::create(array_merge($data, ['student_id' => $studentId]));
    }

    public function createLanguageExam(int $studentId, array $data): LanguageExam
    {
        return LanguageExam::create(array_merge($data, ['student_id' => $studentId]));
    }
}
