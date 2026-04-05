<?php

declare(strict_types=1);

namespace Modules\Audit\Domain\Services;

use App\Core\Audit\Models\AuditLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Audit\Domain\Contracts\AuditLogRepositoryInterface;

final readonly class AuditLogService
{
    public function __construct(
        private AuditLogRepositoryInterface $logs,
    ) {
    }

    /**
     * @param array<string, mixed> $filters
     */
    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return $this->logs->paginate($filters, $perPage);
    }

    public function findOrFail(int $id): AuditLog
    {
        $log = $this->logs->findById($id);

        if ($log === null) {
            abort(404);
        }

        return $log;
    }

    public function delete(AuditLog $auditLog): bool
    {
        return $this->logs->delete($auditLog);
    }
}