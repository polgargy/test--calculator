<?php

namespace Tests\Feature\Subject;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetRequiredSubjectsTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_required_subjects(): void
    {
        $response = $this->getJson(route('required-subjects.index'));

        $response->assertOk();

        $this->assertCount(3, $response->json('data'));

        $names = array_column($response->json('data'), 'name');
        $this->assertContains('magyar nyelv és irodalom', $names);
        $this->assertContains('történelem', $names);
        $this->assertContains('matematika', $names);
    }
}
