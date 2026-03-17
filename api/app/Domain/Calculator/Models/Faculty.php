<?php

namespace App\Domain\Calculator\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Faculty extends Model
{
    protected $fillable = [
        'institution_id',
        'name',
        'required_subject_id',
        'requires_advanced_level',
    ];

    protected $casts = [
        'requires_advanced_level' => 'boolean',
    ];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function requiredSubject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'required_subject_id');
    }

    public function electiveSubjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'elective_subjects')->withTimestamps();
    }
}
