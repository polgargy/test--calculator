<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('required')->default(false);
            $table->timestamps();
        });

        DB::table('subjects')->insert([
            ['id' => 1,  'name' => 'magyar nyelv és irodalom', 'required' => true,  'created_at' => now(), 'updated_at' => now()],
            ['id' => 2,  'name' => 'történelem',               'required' => true,  'created_at' => now(), 'updated_at' => now()],
            ['id' => 3,  'name' => 'matematika',               'required' => true,  'created_at' => now(), 'updated_at' => now()],
            ['id' => 4,  'name' => 'biológia',                 'required' => false, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5,  'name' => 'fizika',                   'required' => false, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6,  'name' => 'informatika',              'required' => false, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7,  'name' => 'kémia',                    'required' => false, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8,  'name' => 'angol',                    'required' => false, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9,  'name' => 'francia',                  'required' => false, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 10, 'name' => 'német',                    'required' => false, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 11, 'name' => 'olasz',                    'required' => false, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 12, 'name' => 'orosz',                    'required' => false, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 13, 'name' => 'spanyol',                  'required' => false, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
