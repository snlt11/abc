<?php

namespace App\Models;

use App\Models\Module;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nuwave\Lighthouse\Execution\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class Permission extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $fillable = [
        'name',
        'description',
        'access_scope',
        'authorizer_id',
        'owner_id',
    ];

    protected $casts = [
        // Add any necessary casts here
    ];

    public function authorizer()
    {
        return $this->belongsTo(User::class, 'authorizer_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function modules()
    {
        return $this->hasMany(Module::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function scopeGetAllPermissions(Builder $query, $rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        return $query->with(['authorizer', 'owner']);
    }
}
