<?php

declare(strict_types=1);

namespace App\Core\RBAC\Middleware;

use App\Core\RBAC\Contracts\RBACResolverInterface;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class AccessControlMiddleware
{
    public function __construct(
        private readonly RBACResolverInterface $resolver,
    ) {
    }

    public function handle(Request $request, Closure $next, string $permissionSlug): Response
    {
        $user = $request->user();

        if ($user === null || !$this->resolver->can($user, $permissionSlug)) {
            abort(Response::HTTP_FORBIDDEN, __('rbac::rbac.forbidden'));
        }

        return $next($request);
    }
}