<?php

declare(strict_types=1);

namespace App\Core\Themes\Http\Controllers;

use App\Core\Audit\Services\AuditLogger;
use App\Core\Themes\Contracts\ThemeManagerInterface;
use App\Core\Themes\Http\Requests\UpdateActiveThemeRequest;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use RuntimeException;

final class ThemeController extends Controller
{
    public function __construct(
        private readonly ThemeManagerInterface $themes,
        private readonly AuditLogger $audit,
    ) {
    }

    public function index(): View
    {
        return view('core-themes::themes.index', [
            'publicThemes' => $this->themes->all('public'),
            'adminThemes' => $this->themes->all('admin'),
            'activePublicTheme' => $this->themes->active('public'),
            'activeAdminTheme' => $this->themes->active('admin'),
        ]);
    }

    public function update(UpdateActiveThemeRequest $request): RedirectResponse
    {
        $payload = $request->validatedPayload();

        try {
            $this->themes->setActive($payload['group'], $payload['theme']);

            $this->audit->log(
                'themes.changed',
                null,
                [
                    'group' => $payload['group'],
                    'theme' => $payload['theme'],
                ],
            );
        } catch (RuntimeException) {
            return redirect()
                ->route('themes.index')
                ->with('error', __('core-themes::themes.invalid_theme'));
        }

        return redirect()
            ->route('themes.index')
            ->with('success', __('core-themes::themes.updated'));
    }
}