<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{
    /**
     * All domain models should define their own $fillable explicitly.
     *
     * @var array<int, string>
     */
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public $timestamps = true;
}