<?php

declare(strict_types=1);

namespace App\Core\Audit\Services;

use App\Core\Audit\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class AuditLogger
{
    public function __construct(
        private readonly Request $request,
    ) {
    }

    /**
     * @param array<string, mixed> $properties
     */
    public function log(string $event, ?Model $subject = null, array $properties = [], ?string $description = null): void
    {
        $actor = Auth::user();

        AuditLog::query()->create([
            'event' => $event,
            'description' => $description,
            'actor_type' => $actor !== null ? $actor::class : null,
            'actor_id' => $actor !== null && method_exists($actor, 'getAuthIdentifier')
                ? (string) $actor->getAuthIdentifier()
                : null,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey() !== null ? (string) $subject->getKey() : null,
            'properties' => $properties,
            'ip_address' => $this->request->ip(),
            'user_agent' => $this->request->userAgent(),
        ]);
    }
}