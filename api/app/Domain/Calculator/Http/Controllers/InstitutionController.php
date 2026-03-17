<?php

namespace App\Domain\Calculator\Http\Controllers;

use App\Domain\Calculator\Resource\InstitutionResource;
use App\Domain\Calculator\Service\InstitutionService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class InstitutionController extends Controller
{
    public function __construct(
        private InstitutionService $institutionService
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return InstitutionResource::collection(
            $this->institutionService->getAll()
        );
    }
}
