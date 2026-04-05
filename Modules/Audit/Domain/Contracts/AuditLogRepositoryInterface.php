<?php

declare(strict_types=1);

namespace Modules\Audit\Domain\Contracts;

use App\Core\Audit\Models\AuditLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AuditLogRepositoryInterface
{
    /**
     * @param array<string, mixed> $filters
     */
    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator;

    public function findById(int $id): ?AuditLog;

    public function delete(AuditLog $auditLog): bool;
}