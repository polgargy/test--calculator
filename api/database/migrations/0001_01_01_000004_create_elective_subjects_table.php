<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('elective_subjects', function (Blueprint $table) {
            $table->foreignId('faculty_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->primary(['faculty_id', 'subject_id']);
            $table->timestamps();
        });

        DB::table('elective_subjects')->insert([
            // ELTE IK – Programtervező informatikus
            ['faculty_id' => 1, 'subject_id' => 4,  'created_at' => now(), 'updated_at' => now()],
            ['faculty_id' => 1, 'subject_id' => 5,  'created_at' => now(), 'updated_at' => now()],
            ['faculty_id' => 1, 'subject_id' => 6,  'created_at' => now(), 'updated_at' => now()],
            ['faculty_id' => 1, 'subject_id' => 7,  'created_at' => now(), 'updated_at' => now()],
            // PPKE BTK – Anglisztika
            ['faculty_id' => 2, 'subject_id' => 2,  'created_at' => now(), 'updated_at' => now()],
            ['faculty_id' => 2, 'subject_id' => 9,  'created_at' => now(), 'updated_at' => now()],
            ['faculty_id' => 2, 'subject_id' => 10, 'created_at' => now(), 'updated_at' => now()],
            ['faculty_id' => 2, 'subject_id' => 11, 'created_at' => now(), 'updated_at' => now()],
            ['faculty_id' => 2, 'subject_id' => 12, 'created_at' => now(), 'updated_at' => now()],
            ['faculty_id' => 2, 'subject_id' => 13, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('elective_subjects');
    }
};
