<?php

declare(strict_types=1);

namespace Modules\Media\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class MediaDeletedEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public int $mediaId,
        public string $path,
        public string $filename,
    ) {
    }
}