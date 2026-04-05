<?php

declare(strict_types=1);

namespace Modules\Roles\Application\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Roles\Domain\Services\RoleAssignmentService;
use Symfony\Component\HttpFoundation\Response;

final class RoleMiddleware
{
    public function __construct(
        private readonly RoleAssignmentService $assignments,
    ) {
    }

    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if ($user === null || ! method_exists($user, 'getAuthIdentifier')) {
            abort(403);
        }

        foreach ($roles as $role) {
            if ($this->assignments->hasRoleForSubject($role, $user::class, $user->getAuthIdentifier())) {
                return $next($request);
            }
        }

        abort(403);
    }
}