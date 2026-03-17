<?php

namespace Tests\Feature\Student;

use App\Domain\Calculator\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class ListStudentsTest extends TestCase
{
    use RefreshDatabase;

    private function getStudents(array $query = []): TestResponse
    {
        $url = route('calculator.students');
        if ($query) {
            $url .= '?'.http_build_query($query);
        }

        return $this->getJson($url);
    }

    private function createStudent(array $overrides = []): Student
    {
        return Student::create(array_merge([
            'name' => 'Teszt Diák',
            'faculty_id' => 1,
            'base_points' => 320,
            'additional_points' => 50,
        ], $overrides));
    }

    public function test_returns_all_students_without_pagination(): void
    {
        $this->createStudent(['name' => 'Diák A']);
        $this->createStudent(['name' => 'Diák B']);

        $this->getStudents()->assertOk();
        $this->assertCount(2, $this->getStudents()->json('data'));
    }

    public function test_returns_paginated_students(): void
    {
        for ($i = 0; $i < 15; $i++) {
            $this->createStudent(['name' => "Diák {$i}"]);
        }

        $response = $this->getStudents(['page' => 1, 'per_page' => 5]);

        $response->assertOk();
        $this->assertArrayHasKey('meta', $response->json());
        $this->assertCount(5, $response->json('data'));
        $this->assertEquals(15, $response->json('meta.total'));
    }

    public function test_student_resource_includes_relations(): void
    {
        $this->createStudent();

        $response = $this->getStudents()->assertOk();

        $this->assertArrayHasKey('faculty', $response->json('data.0'));
        $this->assertEquals(370, $response->json('data.0.total_points'));
    }
}
