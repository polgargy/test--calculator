<?php

namespace App\Domain\Calculator\Service;

use App\Domain\Calculator\Repository\InstitutionRepository;
use Illuminate\Database\Eloquent\Collection;

class InstitutionService
{
    public function __construct(
        private InstitutionRepository $institutionRepository
    ) {}

    public function getAll(): Collection
    {
        return $this->institutionRepository->getAll();
    }
}
