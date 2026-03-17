<?php

namespace App\Domain\Calculator\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Institution extends Model
{
    protected $fillable = ['name'];

    public function faculties(): HasMany
    {
        return $this->hasMany(Faculty::class);
    }
}
