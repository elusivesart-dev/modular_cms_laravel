<?php

declare(strict_types=1);

namespace Modules\Audit\Infrastructure\Repositories;

use App\Core\Audit\Models\AuditLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Audit\Domain\Contracts\AuditLogRepositoryInterface;

final class EloquentAuditLogRepository implements AuditLogRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = AuditLog::query()
            ->with(['actor', 'subject'])
            ->latest('id');

        $event = trim((string) ($filters['event'] ?? ''));
        $search = trim((string) ($filters['search'] ?? ''));

        if ($event !== '') {
            $query->where('event', $event);
        }

        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder
                    ->where('event', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhere('ip_address', 'like', '%' . $search . '%')
                    ->orWhere('actor_type', 'like', '%' . $search . '%')
                    ->orWhere('subject_type', 'like', '%' . $search . '%')
                    ->orWhere('user_agent', 'like', '%' . $search . '%');
            });
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function findById(int $id): ?AuditLog
    {
        return AuditLog::query()->find($id);
    }

    public function delete(AuditLog $auditLog): bool
    {
        return (bool) $auditLog->delete();
    }
}