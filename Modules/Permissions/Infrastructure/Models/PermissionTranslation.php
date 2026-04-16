<?php

declare(strict_types=1);

namespace Modules\Permissions\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class PermissionTranslation extends Model
{
    protected $table = 'permission_translations';

    protected $fillable = [
        'permission_id',
        'locale',
        'label',
        'description',
    ];

    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class, 'permission_id');
    }
}