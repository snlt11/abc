<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class File extends Model
{
    use SoftDeletes, HasUuids;

    protected $fillable = [
        'name',
        'path',
        'folder',
        'extension',
        'image',
        'thumbnail'
    ];

    protected $casts = [
        'image' => 'boolean'
    ];

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }
}