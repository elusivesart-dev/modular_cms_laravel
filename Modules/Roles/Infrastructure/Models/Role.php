<?php

declare(strict_types=1);

namespace Modules\Roles\Infrastructure\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Roles\Infrastructure\Database\Factories\RoleFactory;

final class Role extends Model
{
    use HasFactory;

    protected $table = 'roles';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_system',
    ];

    protected $casts = [
        'is_system' => 'boolean',
    ];

    protected static function newFactory(): RoleFactory
    {
        return RoleFactory::new();
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(RoleAssignment::class, 'role_id');
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            \Modules\Permissions\Infrastructure\Models\Permission::class,
            'role_permissions',
            'role_id',
            'permission_id'
        )->withTimestamps();
    }
}