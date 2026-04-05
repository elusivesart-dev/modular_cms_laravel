<?php

declare(strict_types=1);

namespace Modules\Settings\Domain\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Settings\Domain\DTOs\SettingData;
use Modules\Settings\Infrastructure\Models\Setting;

interface SettingRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function findById(int $id): ?Setting;

    public function findByKey(string $key): ?Setting;

    public function getByGroup(string $group): Collection;

    public function create(SettingData $data): Setting;

    public function update(Setting $setting, SettingData $data): Setting;

    public function delete(Setting $setting): void;

    public function updateGroupValues(string $group, array $values): void;

    public function getDistinctGroups(): Collection;

    public function getRuntimeSettings(): Collection;
}