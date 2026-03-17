<?php

namespace Tests\Feature\Institution;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetInstitutionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_institutions_with_faculties_and_subjects(): void
    {
        $response = $this->getJson(route('institutions.index'));

        $response->assertOk();

        $data = $response->json('data');

        $this->assertCount(2, $data);

        $elteIk = collect($data)->firstWhere('name', 'ELTE IK');
        $this->assertNotNull($elteIk);
        $this->assertArrayHasKey('faculties', $elteIk);
        $this->assertCount(1, $elteIk['faculties']);

        $faculty = $elteIk['faculties'][0];
        $this->assertEquals('Programtervező informatikus', $faculty['name']);
        $this->assertArrayHasKey('required_subject', $faculty);
        $this->assertEquals('matematika', $faculty['required_subject']['name']);
        $this->assertArrayHasKey('elective_subjects', $faculty);
        $this->assertCount(4, $faculty['elective_subjects']);
    }

    public function test_returns_anglisztika_faculty_with_required_advanced_level(): void
    {
        $response = $this->getJson(route('institutions.index'));

        $response->assertOk();

        $data = $response->json('data');

        $ppke = collect($data)->firstWhere('name', 'PPKE BTK');
        $this->assertNotNull($ppke);
        $faculty = $ppke['faculties'][0];
        $this->assertEquals('Anglisztika', $faculty['name']);
        $this->assertTrue($faculty['requires_advanced_level']);
        $this->assertEquals('angol', $faculty['required_subject']['name']);
        $this->assertCount(6, $faculty['elective_subjects']);
    }
}
