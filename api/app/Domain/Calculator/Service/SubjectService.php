<?php

namespace App\Domain\Calculator\Service;

use App\Domain\Calculator\Repository\SubjectRepository;
use Illuminate\Database\Eloquent\Collection;

class SubjectService
{
    public function __construct(
        private SubjectRepository $subjectRepository
    ) {}

    public function getRequired(): Collection
    {
        return $this->subjectRepository->getRequired();
    }
}
