<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Policy extends Model
{
    use SoftDeletes, HasUuids, HasFactory;

    protected $fillable = [
        'name',
        'description',
        'owner_id',
        'is_default',
        'show_all_employees',
        'show_employees',
        'attendance_nullable',
        'nearby_checkin_enable',
        'easy_check_in_range',
        'version',
        'start_date',
        'end_date',
        'authorizer_id',
        'auto_attendance',
        'country_id',
        'payslip_company_name',
        'payslip_logo_id',
        'payslip_company_address',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id', 'id')->withDefault();
    }

    public function authorizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'authorizer_id', 'id')->withDefault();
    }

    public function users(): HasMany
    {
        return $this->hasMany(UserPolicy::class);
    }

    public function payslipLogo(): BelongsTo
    {
        return $this->belongsTo(File::class, 'payslip_logo_id');
    }
}
