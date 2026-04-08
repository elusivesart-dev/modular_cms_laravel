<?php

declare(strict_types=1);

namespace Modules\Settings\Application\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Settings\Application\Services\RuntimeSettingsApplier;
use Symfony\Component\HttpFoundation\Response;

final class ApplySystemSettings
{
    public function __construct(
        private readonly RuntimeSettingsApplier $runtimeSettingsApplier,
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $sessionLocale = session()->get('locale');

        $this->runtimeSettingsApplier->applyLocale(
            is_string($sessionLocale) ? $sessionLocale : null
        );

        return $next($request);
    }
}