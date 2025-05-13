<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Industry extends Model
{
    use SoftDeletes, HasUuids;

    protected $fillable = [
        'name'
    ];

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }
}