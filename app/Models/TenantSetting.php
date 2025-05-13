<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantSetting extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subdomain',
        'owner_id',
        'support_account_enable',
    ];

    /**
     * Get the owner of this tenant setting.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}