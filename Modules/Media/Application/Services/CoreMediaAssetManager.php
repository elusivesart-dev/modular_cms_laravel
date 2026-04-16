<?php

declare(strict_types=1);

namespace Modules\Media\Application\Services;

use App\Core\Media\Contracts\MediaAssetManagerInterface;
use App\Core\Media\DTO\MediaAssetData;
use Illuminate\Http\UploadedFile;
use Modules\Media\Application\Contracts\MediaServiceInterface;
use Modules\Media\Domain\Contracts\MediaRepositoryInterface;
use Modules\Media\Infrastructure\Models\Media;

final readonly class CoreMediaAssetManager implements MediaAssetManagerInterface
{
    public function __construct(
        private MediaServiceInterface $mediaService,
        private MediaRepositoryInterface $mediaRepository,
    ) {
    }

    public function uploadImage(
        UploadedFile $file,
        ?int $uploadedBy = null,
        ?string $title = null,
        ?string $altText = null,
    ): MediaAssetData {
        $media = $this->mediaService->upload(
            file: $file,
            uploadedBy: $uploadedBy,
            title: $title,
            altText: $altText,
        );

        return $this->map($media);
    }

    public function findById(int $id): ?MediaAssetData
    {
        $media = $this->mediaRepository->findById($id);

        if ($media === null) {
            return null;
        }

        return $this->map($media);
    }

    private function map(Media $media): MediaAssetData
    {
        return new MediaAssetData(
            id: (int) $media->getKey(),
            url: $media->url,
            mimeType: $media->mime_type !== null ? (string) $media->mime_type : null,
            title: $media->title !== null ? (string) $media->title : null,
            altText: $media->alt_text !== null ? (string) $media->alt_text : null,
        );
    }
}