<?php

namespace App\Domain\Calculator\Http\Controllers;

use App\Domain\Calculator\Resource\StudentResource;
use App\Domain\Calculator\Service\StudentService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Calculator\CalculateRequest;
use App\Http\Requests\Calculator\ListStudentsRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CalculatorController extends Controller
{
    public function __construct(
        private StudentService $studentService
    ) {}

    public function calculate(CalculateRequest $request): JsonResponse
    {
        $result = $this->studentService->calculate($request->validated());

        return response()->json($result, JsonResponse::HTTP_CREATED);
    }

    public function index(ListStudentsRequest $request): AnonymousResourceCollection
    {
        $perPage = $request->shouldPaginate() ? $request->getPerPage(10) : null;
        $orderBy = $request->getOrderBy('id');
        $order = $request->getOrder('desc');
        $students = $this->studentService->getAll($orderBy, $order, $perPage);

        return StudentResource::collection($students);
    }
}
