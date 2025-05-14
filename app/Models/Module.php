<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nuwave\Lighthouse\Execution\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class Module extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

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

    public function scopeGetAllModules(Builder $query, $rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        return $query->with('permission');
    }
}
