<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Module extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'create',
        'view',
        'update',
        'delete',
        'permission_id',
    ];

    protected $casts = [
        'create' => 'boolean',
        'view' => 'boolean',
        'update' => 'boolean',
        'delete' => 'boolean',
    ];

    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }
}
