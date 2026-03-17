<?php

namespace App\Domain\Calculator\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    protected $fillable = [
        'name',
        'faculty_id',
        'base_points',
        'additional_points',
    ];

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(Result::class);
    }

    public function languageExams(): HasMany
    {
        return $this->hasMany(LanguageExam::class);
    }
}
