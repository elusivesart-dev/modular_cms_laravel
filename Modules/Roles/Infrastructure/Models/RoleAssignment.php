<?php

declare(strict_types=1);

namespace Modules\Roles\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class RoleAssignment extends Model
{
    protected $table = 'role_assignments';

    protected $fillable = [
        'role_id',
        'subject_type',
        'subject_id',
    ];

    public $timestamps = true;

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}