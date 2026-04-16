<?php

declare(strict_types=1);

namespace App\Core\Media\DTO;

final readonly class MediaAssetData
{
    public function __construct(
        public int $id,
        public ?string $url,
        public ?string $mimeType,
        public ?string $title,
        public ?string $altText,
    ) {
    }
}