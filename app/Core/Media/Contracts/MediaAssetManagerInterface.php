<?php

declare(strict_types=1);

namespace App\Core\Media\Contracts;

use App\Core\Media\DTO\MediaAssetData;
use Illuminate\Http\UploadedFile;

interface MediaAssetManagerInterface
{
    public function uploadImage(
        UploadedFile $file,
        ?int $uploadedBy = null,
        ?string $title = null,
        ?string $altText = null,
    ): MediaAssetData;

    public function findById(int $id): ?MediaAssetData;
}