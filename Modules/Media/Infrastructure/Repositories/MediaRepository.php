<?php

declare(strict_types=1);

namespace Modules\Media\Infrastructure\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Media\Domain\Contracts\MediaRepositoryInterface;
use Modules\Media\Domain\DTOs\StoreMediaData;
use Modules\Media\Infrastructure\Models\Media;

final class MediaRepository implements MediaRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 24): LengthAwarePaginator
    {
        $query = Media::query()
            ->with('uploader')
            ->latest('id');

        $search = trim((string) ($filters['search'] ?? ''));
        $mimeType = trim((string) ($filters['mime_type'] ?? ''));

        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder
                    ->where('filename', 'like', '%' . $search . '%')
                    ->orWhere('original_name', 'like', '%' . $search . '%')
                    ->orWhere('title', 'like', '%' . $search . '%')
                    ->orWhere('alt_text', 'like', '%' . $search . '%')
                    ->orWhere('mime_type', 'like', '%' . $search . '%');
            });
        }

        if ($mimeType !== '') {
            $query->where('mime_type', 'like', $mimeType . '%');
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function create(StoreMediaData $data): Media
    {
        return Media::query()->create([
            'disk' => $data->disk,
            'directory' => $data->directory,
            'path' => $data->path,
            'filename' => $data->filename,
            'original_name' => $data->originalName,
            'mime_type' => $data->mimeType,
            'extension' => $data->extension,
            'size' => $data->size,
            'visibility' => $data->visibility,
            'title' => $data->title,
            'alt_text' => $data->altText,
            'uploaded_by' => $data->uploadedBy,
        ]);
    }

    public function findById(int $id): ?Media
    {
        return Media::query()->with('uploader')->find($id);
    }

    public function delete(Media $media): bool
    {
        return (bool) $media->delete();
    }
}