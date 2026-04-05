<?php

declare(strict_types=1);

namespace Modules\Media\Domain\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Media\Domain\DTOs\StoreMediaData;
use Modules\Media\Infrastructure\Models\Media;

interface MediaRepositoryInterface
{
    /**
     * @param array<string, mixed> $filters
     */
    public function paginate(array $filters = [], int $perPage = 24): LengthAwarePaginator;

    public function create(StoreMediaData $data): Media;

    public function findById(int $id): ?Media;

    public function delete(Media $media): bool;
}