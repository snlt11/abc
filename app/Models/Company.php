<?php

namespace App\Models;

use App\Models\File;
use App\Models\Industry;
use App\Models\Location;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Company extends Model
{
    use SoftDeletes, HasUuids;

    protected $fillable = [
        'name',
        'registration_number',
        'founding_year',
        'industry_id',
        'file_id',
        'time_zone',
        'location_id',
    ];

    protected $casts = [
        'founding_year' => 'integer',
    ];

    public function industry(): BelongsTo
    {
        return $this->belongsTo(Industry::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function file()
    {
        return $this->belongsTo(File::class);
    }


}