<?php

declare(strict_types=1);

namespace Modules\Media\Application\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Modules\Media\Infrastructure\Models\Media;

interface MediaServiceInterface
{
    /**
     * @param array<string, mixed> $filters
     */
    public function paginate(array $filters = [], int $perPage = 24): LengthAwarePaginator;

    public function upload(
        UploadedFile $file,
        ?int $uploadedBy = null,
        ?string $title = null,
        ?string $altText = null,
    ): Media;

    public function findOrFail(int $id): Media;

    public function delete(Media $media): bool;
}