<?php

namespace App\Domain\Calculator\Service;

use App\Domain\Calculator\Enums\LanguageLevel;
use App\Domain\Calculator\Models\Faculty;
use App\Domain\Calculator\Repository\FacultyRepository;
use App\Domain\Calculator\Repository\StudentRepository;
use App\Traits\HasPaginatedService;
use Illuminate\Support\Collection as SupportCollection;

class StudentService
{
    use HasPaginatedService;

    public function __construct(
        private StudentRepository $studentRepository,
        private FacultyRepository $facultyRepository,
    ) {}

    protected function getRepository(): StudentRepository
    {
        return $this->studentRepository;
    }

    public function calculate(array $data): array
    {
        $faculty = $this->facultyRepository->findWithElectives($data['faculty_id']);

        $resultsBySubject = collect($data['results'])->keyBy('subject_id');

        $basePoints = $this->calculateBasePoints($faculty, $resultsBySubject);
        $additionalPoints = $this->calculateAdditionalPoints($data['results'], $data['language_exams'] ?? []);

        $student = $this->studentRepository->create([
            'name' => $data['name'],
            'faculty_id' => $faculty->id,
            'base_points' => $basePoints,
            'additional_points' => $additionalPoints,
        ]);

        foreach ($data['results'] as $resultData) {
            $this->studentRepository->createResult($student->id, $resultData);
        }

        foreach ($data['language_exams'] ?? [] as $examData) {
            $this->studentRepository->createLanguageExam($student->id, $examData);
        }

        return ['total_points' => $basePoints + $additionalPoints];
    }

    private function calculateBasePoints(Faculty $faculty, SupportCollection $resultsBySubject): int
    {
        $requiredResult = (int) ($resultsBySubject->get($faculty->required_subject_id)['result'] ?? 0);

        $electiveSubjectIds = $faculty->electiveSubjects->pluck('id')->all();
        $bestElectiveResult = $resultsBySubject
            ->filter(fn ($r) => in_array($r['subject_id'], $electiveSubjectIds))
            ->max('result') ?? 0;

        return ($requiredResult + (int) $bestElectiveResult) * 2;
    }

    private function calculateAdditionalPoints(array $results, array $languageExams): int
    {
        $points = 0;

        // Language exam points – only highest level per language
        $groupedByLanguage = collect($languageExams)->groupBy('language');
        foreach ($groupedByLanguage as $exams) {
            $hasC1 = $exams->contains(fn ($e) => LanguageLevel::from($e['level']) === LanguageLevel::C1);
            $points += $hasC1 ? LanguageLevel::C1->points() : LanguageLevel::B2->points();
        }

        // Advanced-level exam bonus
        $advancedCount = collect($results)->filter(fn ($r) => (bool) $r['advanced_level'])->count();
        $points += $advancedCount * 50;

        return min($points, 100);
    }
}
