<?php

namespace Tests\Feature\Student;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class CalculatePointsTest extends TestCase
{
    use RefreshDatabase;

    private function calculate(array $overrides = []): TestResponse
    {
        return $this->postJson(route('calculator.calculate'), $this->validPayload($overrides));
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Teszt Diák',
            'institution_id' => 1,
            'faculty_id' => 1,
            'results' => [
                ['subject_id' => 1, 'advanced_level' => false, 'result' => 75], // magyar
                ['subject_id' => 2, 'advanced_level' => false, 'result' => 80], // történelem
                ['subject_id' => 3, 'advanced_level' => false, 'result' => 90], // matematika (required)
                ['subject_id' => 5, 'advanced_level' => false, 'result' => 70], // fizika (elective)
            ],
            'language_exams' => [],
        ], $overrides);
    }

    public function test_calculates_base_points_correctly(): void
    {
        // base = (matematika:90 + fizika:70) * 2 = 320
        $this->calculate()->assertCreated()->assertJson(['total_points' => 320]);
    }

    public function test_advanced_plus_two_language_exams(): void
    {
        // base = (matematika:90 + informatika:95) * 2 = 370
        // additional = min(advanced:50 + B2:28 + C1:40, 100) = 100
        // total = 470
        $this->calculate([
            'results' => [
                ['subject_id' => 1, 'advanced_level' => false, 'result' => 70], // magyar
                ['subject_id' => 2, 'advanced_level' => false, 'result' => 80], // történelem
                ['subject_id' => 3, 'advanced_level' => true,  'result' => 90], // matematika (required, advanced) +50
                ['subject_id' => 6, 'advanced_level' => false, 'result' => 95], // informatika (elective)
                ['subject_id' => 8, 'advanced_level' => false, 'result' => 94], // angol (not ELTE IK elective, ignored)
            ],
            'language_exams' => [
                ['language' => 'angol', 'level' => 'B2'], // +28
                ['language' => 'nemet', 'level' => 'C1'], // +40
            ],
        ])->assertCreated()->assertJson(['total_points' => 470]);
    }

    public function test_best_elective_wins_over_informatika(): void
    {
        // base = (matematika:90 + fizika:98) * 2 = 376  (fizika beats informatika:95)
        // additional = min(advanced:50 + B2:28 + C1:40, 100) = 100
        // total = 476
        $this->calculate([
            'results' => [
                ['subject_id' => 1, 'advanced_level' => false, 'result' => 70], // magyar
                ['subject_id' => 2, 'advanced_level' => false, 'result' => 80], // történelem
                ['subject_id' => 3, 'advanced_level' => true,  'result' => 90], // matematika (required, advanced) +50
                ['subject_id' => 5, 'advanced_level' => false, 'result' => 98], // fizika (elective)
                ['subject_id' => 6, 'advanced_level' => false, 'result' => 95], // informatika (elective)
                ['subject_id' => 8, 'advanced_level' => false, 'result' => 94], // angol (not ELTE IK elective, ignored)
            ],
            'language_exams' => [
                ['language' => 'angol', 'level' => 'B2'], // +28
                ['language' => 'nemet', 'level' => 'C1'], // +40
            ],
        ])->assertCreated()->assertJson(['total_points' => 476]);
    }

    public function test_picks_best_elective_for_base_points(): void
    {
        // base = (90 + 85) * 2 = 350
        $this->calculate(['results' => [
            ['subject_id' => 1, 'advanced_level' => false, 'result' => 75],
            ['subject_id' => 2, 'advanced_level' => false, 'result' => 80],
            ['subject_id' => 3, 'advanced_level' => false, 'result' => 90],
            ['subject_id' => 5, 'advanced_level' => false, 'result' => 60],
            ['subject_id' => 6, 'advanced_level' => false, 'result' => 85], // better elective
        ]])->assertCreated()->assertJson(['total_points' => 350]);
    }

    public function test_adds_advanced_level_bonus(): void
    {
        // base = 320, additional = 50 → total = 370
        $this->calculate(['results' => [
            ['subject_id' => 1, 'advanced_level' => false, 'result' => 75],
            ['subject_id' => 2, 'advanced_level' => false, 'result' => 80],
            ['subject_id' => 3, 'advanced_level' => true,  'result' => 90], // advanced +50
            ['subject_id' => 5, 'advanced_level' => false, 'result' => 70],
        ]])->assertCreated()->assertJson(['total_points' => 370]);
    }

    public function test_adds_b2_language_exam_bonus(): void
    {
        // base = 320, additional = 28 → total = 348
        $this->calculate(['language_exams' => [
            ['language' => 'angol', 'level' => 'B2'],
        ]])->assertCreated()->assertJson(['total_points' => 348]);
    }

    public function test_adds_c1_language_exam_bonus(): void
    {
        // base = 320, additional = 40 → total = 360
        $this->calculate(['language_exams' => [
            ['language' => 'angol', 'level' => 'C1'],
        ]])->assertCreated()->assertJson(['total_points' => 360]);
    }

    public function test_takes_highest_level_for_same_language(): void
    {
        // Only C1 counts (+40) for english, not both → total = 360
        $this->calculate(['language_exams' => [
            ['language' => 'angol', 'level' => 'B2'],
            ['language' => 'angol', 'level' => 'C1'],
        ]])->assertCreated()->assertJson(['total_points' => 360]);
    }

    public function test_additional_points_capped_at_100(): void
    {
        // base = 320, additional = min(3×50 + 40 + 40, 100) = 100 → total = 420
        $this->calculate([
            'results' => [
                ['subject_id' => 1, 'advanced_level' => true,  'result' => 75],
                ['subject_id' => 2, 'advanced_level' => true,  'result' => 80],
                ['subject_id' => 3, 'advanced_level' => true,  'result' => 90],
                ['subject_id' => 5, 'advanced_level' => false, 'result' => 70],
            ],
            'language_exams' => [
                ['language' => 'angol', 'level' => 'C1'],
                ['language' => 'nemet', 'level' => 'C1'],
            ],
        ])->assertCreated()->assertJson(['total_points' => 420]);
    }

    public function test_saves_student_to_database(): void
    {
        $this->calculate();

        $this->assertDatabaseHas('students', ['name' => 'Teszt Diák', 'faculty_id' => 1]);
    }

    public function test_saves_results_to_database(): void
    {
        $this->calculate();

        $this->assertDatabaseCount('results', 4);
    }

    public function test_saves_language_exams_to_database(): void
    {
        $this->calculate(['language_exams' => [
            ['language' => 'angol', 'level' => 'B2'],
        ]]);

        $this->assertDatabaseHas('language_exams', ['language' => 'angol', 'level' => 'B2']);
    }

    public function test_returns_error_when_no_elective_subject_provided(): void
    {
        // Only global required + faculty required (matematika), no elective (fizika/biológia/etc.)
        $this->calculate(['results' => [
            ['subject_id' => 1, 'advanced_level' => false, 'result' => 75],
            ['subject_id' => 2, 'advanced_level' => false, 'result' => 80],
            ['subject_id' => 3, 'advanced_level' => false, 'result' => 90],
        ]])->assertUnprocessable()->assertJsonValidationErrors(['calculation']);
    }

    public function test_returns_error_when_required_subjects_missing(): void
    {
        // missing magyar (1) and történelem (2)
        $this->calculate(['results' => [
            ['subject_id' => 3, 'advanced_level' => false, 'result' => 90],
            ['subject_id' => 5, 'advanced_level' => false, 'result' => 70],
        ]])->assertUnprocessable()->assertJsonValidationErrors(['calculation']);
    }

    public function test_returns_error_when_result_below_20_percent(): void
    {
        $this->calculate(['results' => [
            ['subject_id' => 1, 'advanced_level' => false, 'result' => 75],
            ['subject_id' => 2, 'advanced_level' => false, 'result' => 80],
            ['subject_id' => 3, 'advanced_level' => false, 'result' => 19], // below 20%
            ['subject_id' => 5, 'advanced_level' => false, 'result' => 70],
        ]])->assertUnprocessable()->assertJsonValidationErrors(['calculation']);
    }

    public function test_returns_error_when_faculty_not_in_institution(): void
    {
        $this->calculate([
            'institution_id' => 2, // PPKE BTK
            'faculty_id' => 1,     // ELTE IK faculty
        ])->assertUnprocessable()->assertJsonValidationErrors(['faculty_id']);
    }

    public function test_english_studies_requires_english_subject(): void
    {
        // angol (8) missing – required by faculty
        $this->postJson(route('calculator.calculate'), [
            'name' => 'Teszt Diák',
            'institution_id' => 2,
            'faculty_id' => 2,
            'results' => [
                ['subject_id' => 1, 'advanced_level' => false, 'result' => 75],
                ['subject_id' => 2, 'advanced_level' => false, 'result' => 80],
                ['subject_id' => 3, 'advanced_level' => false, 'result' => 70],
            ],
            'language_exams' => [],
        ])->assertUnprocessable()->assertJsonValidationErrors(['calculation']);
    }

    public function test_english_studies_requires_advanced_level_for_required_subject(): void
    {
        // angol (8) present but without advanced_level — should fail
        $this->postJson(route('calculator.calculate'), [
            'name' => 'Teszt Diák',
            'institution_id' => 2,
            'faculty_id' => 2,
            'results' => [
                ['subject_id' => 1,  'advanced_level' => false, 'result' => 75],
                ['subject_id' => 2,  'advanced_level' => false, 'result' => 80],
                ['subject_id' => 3,  'advanced_level' => false, 'result' => 70],
                ['subject_id' => 8,  'advanced_level' => false, 'result' => 90], // advanced level missing
            ],
            'language_exams' => [],
        ])->assertUnprocessable()->assertJsonValidationErrors(['calculation']);
    }

    public function test_english_studies_calculates_correctly(): void
    {
        // base = (angol:90 + történelem:80) * 2 = 340
        // additional = min(40 + 50, 100) = 90 → total = 430
        $this->postJson(route('calculator.calculate'), [
            'name' => 'Teszt Diák',
            'institution_id' => 2,
            'faculty_id' => 2,
            'results' => [
                ['subject_id' => 1,  'advanced_level' => false, 'result' => 75], // magyar
                ['subject_id' => 2,  'advanced_level' => false, 'result' => 80], // történelem (elective)
                ['subject_id' => 3,  'advanced_level' => false, 'result' => 70], // matematika
                ['subject_id' => 8,  'advanced_level' => true,  'result' => 90], // angol (required) +50
                ['subject_id' => 10, 'advanced_level' => false, 'result' => 65], // német (elective)
            ],
            'language_exams' => [
                ['language' => 'angol', 'level' => 'C1'], // +40
            ],
        ])->assertCreated()->assertJson(['total_points' => 430]);
    }
}
