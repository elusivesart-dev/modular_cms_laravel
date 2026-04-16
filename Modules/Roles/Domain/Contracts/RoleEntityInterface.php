<?php

declare(strict_types=1);

namespace Modules\Roles\Domain\Contracts;

interface RoleEntityInterface
{
    public function getKey();

    public function getName(): string;

    public function getSlug(): string;

    public function getDescription(): ?string;

    public function isSystem(): bool;

    public function getTranslatedName(string $locale): ?string;

    public function getTranslatedDescription(string $locale): ?string;

    /**
     * @return array<int, int>
     */
    public function getSelectedPermissionIds(): array;
}