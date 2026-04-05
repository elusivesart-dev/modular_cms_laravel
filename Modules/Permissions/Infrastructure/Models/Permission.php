<?php

declare(strict_types=1);

namespace Modules\Permissions\Infrastructure\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Permissions\Infrastructure\Database\Factories\PermissionFactory;
use Modules\Roles\Infrastructure\Models\Role;

final class Permission extends Model
{
    use HasFactory;

    protected $table = 'permissions';

    protected $fillable = [
        'name',
        'label',
        'description',
    ];

    protected static function newFactory(): Factory
    {
        return PermissionFactory::new();
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class,
            'role_permissions',
            'permission_id',
            'role_id'
        )->withTimestamps();
    }
}