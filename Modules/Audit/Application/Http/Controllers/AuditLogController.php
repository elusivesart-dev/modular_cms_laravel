<?php

declare(strict_types=1);

namespace Modules\Audit\Application\Http\Controllers;

use App\Core\Audit\Models\AuditLog;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Audit\Application\Support\AuditLogFormatter;
use Modules\Audit\Domain\Services\AuditLogService;

final class AuditLogController extends Controller
{
    public function __construct(
        private readonly AuditLogService $logs,
        private readonly AuditLogFormatter $formatter,
    ) {
        $this->authorizeResource(AuditLog::class, 'audit_log');
    }

    public function index(Request $request): View
    {
        $filters = [
            'event' => $request->string('event')->toString(),
            'search' => $request->string('search')->toString(),
        ];

        $logs = $this->logs->paginate($filters, 20);

        $formattedLogs = $logs->getCollection()
            ->map(fn (AuditLog $log): array => $this->formatter->format($log));

        $logs->setCollection($formattedLogs);

        return view('audit::audit.index', [
            'logs' => $logs,
            'filters' => $filters,
        ]);
    }

    public function destroy(AuditLog $auditLog): RedirectResponse
    {
        $this->authorize('delete', $auditLog);

        $this->logs->delete($auditLog);

        return redirect()
            ->route('audit.index')
            ->with('success', __('audit::audit.deleted'));
    }
	
	public function bulkDelete(Request $request): RedirectResponse
	{
		$ids = array_filter(array_map('intval', (array) $request->input('ids', [])));

		if ($ids === []) {
			return redirect()
				->route('audit.index')
				->with('error', __('audit::audit.no_selection'));
		}

		foreach ($ids as $id) {
			$log = $this->logs->findOrFail($id);

			$this->authorize('delete', $log);

			$this->logs->delete($log);
		}

		return redirect()
			->route('audit.index')
			->with('success', __('audit::audit.deleted_multiple'));
	}

}