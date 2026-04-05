<?php

declare(strict_types=1);

namespace App\Core\Localization\Models;

use Illuminate\Database\Eloquent\Model;

final class Language extends Model
{
    protected $table = 'languages';

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'name',
        'native_name',
        'direction',
        'version',
        'installed_path',
        'is_active',
        'is_system',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'is_system' => 'boolean',
    ];
}