<?php

declare(strict_types=1);

namespace Modules\Settings\Infrastructure\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Settings\Infrastructure\Database\Factories\SettingFactory;

final class Setting extends Model
{
    use HasFactory;

    protected $table = 'settings';

    protected $fillable = [
        'group',
        'key',
        'value',
        'type',
        'label',
        'description',
        'is_public',
        'is_system',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'is_system' => 'boolean',
    ];

    protected static function newFactory(): Factory
    {
        return SettingFactory::new();
    }
}