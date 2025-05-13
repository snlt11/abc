<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use SoftDeletes, HasUuids;

    protected $fillable = [
        'name',
        'google_map_address',
        'lat',
        'lng',
        'location_qr'
    ];

    protected $casts = [
        'lat' => 'decimal:8',
        'lng' => 'decimal:8'
    ];

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }
}