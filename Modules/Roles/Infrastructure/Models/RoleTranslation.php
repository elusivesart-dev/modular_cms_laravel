<?php

declare(strict_types=1);

namespace Modules\Roles\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class RoleTranslation extends Model
{
    protected $table = 'role_translations';

    protected $fillable = [
        'role_id',
        'locale',
        'name',
        'description',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}