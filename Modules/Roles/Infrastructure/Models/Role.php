<?php

declare(strict_types=1);

namespace Modules\Roles\Infrastructure\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Roles\Domain\Contracts\RoleEntityInterface;
use Modules\Roles\Infrastructure\Database\Factories\RoleFactory;

final class Role extends Model implements RoleEntityInterface
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

    public function translations(): HasMany
    {
        return $this->hasMany(RoleTranslation::class, 'role_id');
    }

    public function getName(): string
    {
        return (string) $this->name;
    }

    public function getSlug(): string
    {
        return (string) $this->slug;
    }

    public function getDescription(): ?string
    {
        return $this->description !== null ? (string) $this->description : null;
    }

    public function isSystem(): bool
    {
        return (bool) $this->is_system;
    }

    public function getTranslatedName(string $locale): ?string
    {
        $translation = $this->translationFor($locale);

        return $translation?->name !== null ? (string) $translation->name : null;
    }

    public function getTranslatedDescription(string $locale): ?string
    {
        $translation = $this->translationFor($locale);

        return $translation?->description !== null ? (string) $translation->description : null;
    }

    public function getSelectedPermissionIds(): array
    {
        return $this->permissions()
            ->pluck('permissions.id')
            ->map(static fn (mixed $id): int => (int) $id)
            ->all();
    }

    private function translationFor(string $locale): ?RoleTranslation
    {
        if ($this->relationLoaded('translations')) {
            /** @var Collection<int, RoleTranslation> $translations */
            $translations = $this->getRelation('translations');

            return $translations->firstWhere('locale', $locale);
        }

        /** @var RoleTranslation|null $translation */
        $translation = $this->translations()
            ->where('locale', $locale)
            ->first();

        return $translation;
    }
}