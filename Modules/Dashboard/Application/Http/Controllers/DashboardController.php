<?php

declare(strict_types=1);

namespace Modules\Dashboard\Application\Http\Controllers;

use App\Core\Audit\Models\AuditLog;
use App\Core\Modules\Registry\ModuleRegistry;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Modules\Audit\Application\Support\AuditLogFormatter;
use Modules\Users\Infrastructure\Models\User;

final class DashboardController extends Controller
{
    public function __construct(
        private readonly ModuleRegistry $moduleRegistry,
        private readonly AuditLogFormatter $auditLogFormatter,
    ) {
    }

    public function index(): View
    {
        $modulesCount = count($this->moduleRegistry->all());
        $usersCount = User::query()->count();
        $languagesCount = $this->resolveLanguagesCount();
        $themesCount = $this->resolveThemesCount();

        $recentActions = AuditLog::query()
            ->with(['actor.avatarMedia', 'subject'])
            ->latest('id')
            ->paginate(5, ['*'], 'recent_actions_page');

        $recentActions->setCollection(
            $recentActions->getCollection()->map(
                fn (AuditLog $log): array => $this->auditLogFormatter->format($log)
            )
        );

        return view('dashboard::dashboard.index', [
            'modulesCount' => $modulesCount,
            'usersCount' => $usersCount,
            'languagesCount' => $languagesCount,
            'themesCount' => $themesCount,
            'recentActions' => $recentActions,
        ]);
    }

    private function resolveLanguagesCount(): int
    {
        $schema = DB::getSchemaBuilder();

        if (! $schema->hasTable('languages')) {
            return 0;
        }

        return (int) DB::table('languages')->count();
    }

    private function resolveThemesCount(): int
    {
        $themesRoot = config('themes.path', base_path('themes'));

        if (! is_string($themesRoot) || $themesRoot === '' || ! is_dir($themesRoot)) {
            return 0;
        }

        $count = 0;
        $groups = scandir($themesRoot);

        if ($groups === false) {
            return 0;
        }

        foreach ($groups as $group) {
            if ($group === '.' || $group === '..') {
                continue;
            }

            $groupPath = $themesRoot . DIRECTORY_SEPARATOR . $group;

            if (! is_dir($groupPath)) {
                continue;
            }

            $themes = scandir($groupPath);

            if ($themes === false) {
                continue;
            }

            foreach ($themes as $theme) {
                if ($theme === '.' || $theme === '..') {
                    continue;
                }

                $themePath = $groupPath . DIRECTORY_SEPARATOR . $theme;

                if (is_dir($themePath)) {
                    $count++;
                }
            }
        }

        return $count;
    }
}