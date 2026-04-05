<?php

declare(strict_types=1);

namespace Modules\Media\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Media\Infrastructure\Models\Media;

final class MediaUploadedEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public Media $media,
    ) {
    }
}