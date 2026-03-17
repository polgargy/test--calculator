<?php

namespace App\Domain\Calculator\Models;

use App\Domain\Calculator\Enums\Language;
use App\Domain\Calculator\Enums\LanguageLevel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LanguageExam extends Model
{
    protected $fillable = [
        'student_id',
        'language',
        'level',
    ];

    protected $casts = [
        'language' => Language::class,
        'level' => LanguageLevel::class,
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
