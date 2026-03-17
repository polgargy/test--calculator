<?php

namespace App\Domain\Calculator\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Subject extends Model
{
    protected $fillable = ['name', 'required'];

    protected $casts = [
        'required' => 'boolean',
    ];

    public function faculties(): BelongsToMany
    {
        return $this->belongsToMany(Faculty::class, 'elective_subjects')->withTimestamps();
    }
}
