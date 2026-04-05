<?php

declare(strict_types=1);

namespace Modules\Media\Application\Services;

use App\Core\Audit\Services\AuditLogger;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Filesystem\Factory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Modules\Media\Application\Contracts\MediaServiceInterface;
use Modules\Media\Domain\Contracts\MediaRepositoryInterface;
use Modules\Media\Domain\DTOs\StoreMediaData;
use Modules\Media\Domain\Events\MediaDeletedEvent;
use Modules\Media\Domain\Events\MediaUploadedEvent;
use Modules\Media\Infrastructure\Models\Media;
use RuntimeException;

final class MediaService implements MediaServiceInterface
{
    public function __construct(
        private readonly MediaRepositoryInterface $media,
        private readonly Factory $filesystem,
        private readonly AuditLogger $audit,
    ) {
    }

    public function paginate(array $filters = [], int $perPage = 24): LengthAwarePaginator
    {
        return $this->media->paginate($filters, $perPage);
    }

    public function upload(
        UploadedFile $file,
        ?int $uploadedBy = null,
        ?string $title = null,
        ?string $altText = null,
    ): Media {
        $disk = (string) config('media.disk', 'public');
        $directoryRoot = trim((string) config('media.directory', 'media'), '/');
        $visibility = (string) config('media.visibility', 'public');
        $directory = $directoryRoot . '/' . now()->format('Y/m');

        $extension = strtolower((string) $file->getClientOriginalExtension());
        $originalName = (string) $file->getClientOriginalName();
        $filename = $this->generateFilename($originalName, $extension);

        $storedPath = $this->filesystem
            ->disk($disk)
            ->putFileAs($directory, $file, $filename, [
                'visibility' => $visibility,
            ]);

        if (!is_string($storedPath) || $storedPath === '') {
            throw new RuntimeException(__('media::media.messages.upload_failed'));
        }

        $media = $this->media->create(new StoreMediaData(
            disk: $disk,
            directory: $directory,
            path: $storedPath,
            filename: $filename,
            originalName: $originalName,
            mimeType: (string) ($file->getMimeType() ?? 'application/octet-stream'),
            extension: $extension,
            size: (int) $file->getSize(),
            visibility: $visibility,
            uploadedBy: $uploadedBy,
            title: $title !== null && $title !== '' ? $title : null,
            altText: $altText !== null && $altText !== '' ? $altText : null,
        ));

        event(new MediaUploadedEvent($media));

        $this->audit->log(
            'media.uploaded',
            $media,
            [
                'filename' => $media->filename,
                'original_name' => $media->original_name,
                'mime_type' => $media->mime_type,
                'size' => $media->size,
                'disk' => $media->disk,
                'path' => $media->path,
            ],
        );

        return $media;
    }

    public function findOrFail(int $id): Media
    {
        $media = $this->media->findById($id);

        if ($media === null) {
            throw (new ModelNotFoundException())->setModel(Media::class, [$id]);
        }

        return $media;
    }

    public function delete(Media $media): bool
    {
        $deleted = false;

        if ($this->filesystem->disk($media->disk)->exists($media->path)) {
            $deleted = $this->filesystem->disk($media->disk)->delete($media->path);
        } else {
            $deleted = true;
        }

        if (!$deleted) {
            throw new RuntimeException(__('media::media.messages.delete_failed'));
        }

        $mediaId = (int) $media->getKey();
        $path = (string) $media->path;
        $filename = (string) $media->filename;

        $repositoryDeleted = $this->media->delete($media);

        if ($repositoryDeleted) {
            event(new MediaDeletedEvent(
                mediaId: $mediaId,
                path: $path,
                filename: $filename,
            ));

            $this->audit->log(
                'media.deleted',
                null,
                [
                    'media_id' => $mediaId,
                    'filename' => $filename,
                    'path' => $path,
                ],
            );
        }

        return $repositoryDeleted;
    }

    private function generateFilename(string $originalName, string $extension): string
    {
        $name = pathinfo($originalName, PATHINFO_FILENAME);
        $name = Str::slug($name);

        if ($name === '') {
            $name = 'file';
        }

        $suffix = Str::lower(Str::random(12));

        return $extension !== ''
            ? sprintf('%s-%s.%s', $name, $suffix, $extension)
            : sprintf('%s-%s', $name, $suffix);
    }
}