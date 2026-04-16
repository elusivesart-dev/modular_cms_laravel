<?php

declare(strict_types=1);

namespace Modules\Permissions\Infrastructure\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;
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

    public function translations(): HasMany
    {
        return $this->hasMany(PermissionTranslation::class, 'permission_id');
    }

    public function getTranslatedLabel(string $locale): ?string
    {
        if (! Schema::hasTable('permission_translations')) {
            return null;
        }

        $translation = $this->translationFor($locale);

        return $translation?->label !== null ? (string) $translation->label : null;
    }

    public function getTranslatedDescription(string $locale): ?string
    {
        if (! Schema::hasTable('permission_translations')) {
            return null;
        }

        $translation = $this->translationFor($locale);

        return $translation?->description !== null ? (string) $translation->description : null;
    }

    private function translationFor(string $locale): ?PermissionTranslation
    {
        if (! Schema::hasTable('permission_translations')) {
            return null;
        }

        if ($this->relationLoaded('translations')) {
            /** @var Collection<int, PermissionTranslation> $translations */
            $translations = $this->getRelation('translations');

            return $translations->firstWhere('locale', $locale);
        }

        /** @var PermissionTranslation|null $translation */
        $translation = $this->translations()
            ->where('locale', $locale)
            ->first();

        return $translation;
    }
}