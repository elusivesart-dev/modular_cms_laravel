<?php

declare(strict_types=1);

namespace App\Core\Localization\Http\Middleware;

use App\Core\Localization\Services\LocalizationAuthorizer;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class LocalizationPermissionMiddleware
{
    public function __construct(
        private LocalizationAuthorizer $authorizer,
    ) {
    }

    public function handle(Request $request, Closure $next, string $ability = 'view'): Response
    {
        $user = $request->user();

        if ($user === null) {
            abort(403);
        }

        $allowed = match ($ability) {
            'view' => $this->authorizer->canView($user),
            'install' => $this->authorizer->canInstall($user),
            'delete' => $this->authorizer->canDelete($user),
            'update' => $this->authorizer->canUpdateDefaultLocale($user),
            'manage' => $this->authorizer->canAny($user, [
                'localization.manage',
                'settings.update',
            ]),
            default => false,
        };

        if (!$allowed) {
            abort(403);
        }

        return $next($request);
    }
}