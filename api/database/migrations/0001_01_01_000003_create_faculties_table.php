<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faculties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->foreignId('required_subject_id')->constrained('subjects');
            $table->boolean('requires_advanced_level')->default(false);
            $table->timestamps();
        });

        DB::table('faculties')->insert([
            [
                'id' => 1,
                'institution_id' => 1,
                'name' => 'Programtervező informatikus',
                'required_subject_id' => 3,
                'requires_advanced_level' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'institution_id' => 2,
                'name' => 'Anglisztika',
                'required_subject_id' => 8,
                'requires_advanced_level' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('faculties');
    }
};
