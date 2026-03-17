<?php

namespace App\Http\Requests\Calculator;

use App\Domain\Calculator\Enums\Language;
use App\Domain\Calculator\Enums\LanguageLevel;
use App\Domain\Calculator\Models\Faculty;
use App\Domain\Calculator\Repository\FacultyRepository;
use App\Domain\Calculator\Repository\SubjectRepository;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class CalculateRequest extends FormRequest
{
    public function __construct(
        private readonly FacultyRepository $facultyRepository,
        private readonly SubjectRepository $subjectRepository,
    ) {
        parent::__construct();
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'institution_id' => ['required', 'integer', 'exists:institutions,id'],
            'faculty_id' => ['required', 'integer', 'exists:faculties,id'],
            'results' => ['required', 'array', 'min:1'],
            'results.*.subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'results.*.advanced_level' => ['required', 'boolean'],
            'results.*.result' => ['required', 'integer', 'min:0', 'max:100'],
            'language_exams' => ['nullable', 'array'],
            'language_exams.*.language' => ['required', Rule::enum(Language::class)],
            'language_exams.*.level' => ['required', Rule::enum(LanguageLevel::class)],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->any()) {
                    return;
                }

                $faculty = $this->resolveFaculty($validator);
                if (!$faculty) {
                    return;
                }

                if (!$this->hasAllRequiredSubjects($faculty)) {
                    $validator->errors()->add('calculation', 'Pontszámítás nem lehetséges: hiányzó kötelező tantárgy');

                    return;
                }

                if (!$this->allResultsAboveMinimum()) {
                    $validator->errors()->add('calculation', 'Pontszámítás nem lehetséges: 20% alatti eredmény');

                    return;
                }

                if (!$this->meetsAdvancedLevelRequirement($faculty)) {
                    $validator->errors()->add('calculation', 'Pontszámítás nem lehetséges: emelt szintű követelmény nem teljesült');

                    return;
                }

                if (!$this->hasElectiveSubject($faculty)) {
                    $validator->errors()->add('calculation', 'Pontszámítás nem lehetséges: legalább egy választható tantárgy eredményét meg kell adni');
                }
            },
        ];
    }

    private function resolveFaculty(Validator $validator): ?Faculty
    {
        $faculty = $this->facultyRepository->findWithElectives((int) $this->input('faculty_id'));

        if (!$faculty || $faculty->institution_id !== (int) $this->input('institution_id')) {
            $validator->errors()->add('faculty_id', 'A kiválasztott szak nem tartozik a megadott intézményhez.');

            return null;
        }

        return $faculty;
    }

    private function providedSubjectIds(): array
    {
        return collect($this->input('results', []))
            ->pluck('subject_id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    private function hasAllRequiredSubjects(Faculty $faculty): bool
    {
        $provided = $this->providedSubjectIds();
        $required = $this->subjectRepository->getRequired()->pluck('id')->all();

        return empty(array_diff($required, $provided))
            && in_array($faculty->required_subject_id, $provided);
    }

    private function allResultsAboveMinimum(): bool
    {
        foreach ($this->input('results', []) as $result) {
            if ((int) $result['result'] < 20) {
                return false;
            }
        }

        return true;
    }

    private function meetsAdvancedLevelRequirement(Faculty $faculty): bool
    {
        if (!$faculty->requires_advanced_level) {
            return true;
        }

        $result = collect($this->input('results', []))->first(
            fn ($r) => (int) $r['subject_id'] === (int) $faculty->required_subject_id
        );

        return $result && (bool) $result['advanced_level'];
    }

    private function hasElectiveSubject(Faculty $faculty): bool
    {
        $electiveIds = $faculty->electiveSubjects
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        return !empty(array_intersect($this->providedSubjectIds(), $electiveIds));
    }

    public function attributes(): array
    {
        return [
            'name' => 'név',
            'institution_id' => 'intézmény',
            'faculty_id' => 'szak',
            'results' => 'eredmények',
            'results.*.subject_id' => 'tantárgy',
            'results.*.advanced_level' => 'emelt szint',
            'results.*.result' => 'eredmény',
            'language_exams.*.language' => 'nyelv',
            'language_exams.*.level' => 'szint',
        ];
    }
}
