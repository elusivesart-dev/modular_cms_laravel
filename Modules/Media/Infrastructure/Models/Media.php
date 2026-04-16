<?php

declare(strict_types=1);

namespace Modules\Media\Infrastructure\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Media\Infrastructure\Database\Factories\MediaFactory;

final class Media extends Model
{
    use HasFactory;

    protected $table = 'media';

    protected $fillable = [
        'disk',
        'directory',
        'path',
        'filename',
        'original_name',
        'mime_type',
        'extension',
        'size',
        'visibility',
        'title',
        'alt_text',
        'uploaded_by',
    ];

    protected $casts = [
        'size' => 'integer',
        'uploaded_by' => 'integer',
    ];

    protected $appends = [
        'url',
        'human_size',
    ];

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getUrlAttribute(): ?string
    {
        if ($this->visibility !== 'public') {
            return null;
        }

        $relativePath = 'storage/' . ltrim((string) $this->path, '/');

        return url($relativePath);
    }

    public function getHumanSizeAttribute(): string
    {
        $size = (int) $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];

        $power = 0;

        while ($size >= 1024 && $power < count($units) - 1) {
            $size /= 1024;
            $power++;
        }

        return number_format((float) $size, $power === 0 ? 0 : 2) . ' ' . $units[$power];
    }

    protected static function newFactory(): MediaFactory
    {
        return MediaFactory::new();
    }
}