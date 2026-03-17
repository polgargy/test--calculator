<?php

namespace App\Domain\Calculator\Http\Controllers;

use App\Domain\Calculator\Resource\SubjectResource;
use App\Domain\Calculator\Service\SubjectService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SubjectController extends Controller
{
    public function __construct(
        private SubjectService $subjectService
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return SubjectResource::collection($this->subjectService->getRequired());
    }
}
