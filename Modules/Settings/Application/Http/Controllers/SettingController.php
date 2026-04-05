<?php

declare(strict_types=1);

namespace Modules\Settings\Application\Http\Controllers;

use App\Core\Localization\Contracts\LanguageArchiveInstallerInterface;
use App\Core\Localization\Contracts\LanguageDeleterInterface;
use App\Core\Localization\Contracts\LanguageRegistryInterface;
use App\Core\Localization\Contracts\LanguageRepositoryInterface;
use App\Core\Localization\Events\DefaultLocaleChangedEvent;
use App\Core\Localization\Events\LanguageDeletedEvent;
use App\Core\Localization\Events\LanguageInstalledEvent;
use App\Core\Localization\Exceptions\InvalidLanguageManifestException;
use App\Core\Localization\Exceptions\LanguageAlreadyExistsException;
use App\Core\Localization\Exceptions\LanguageDeletionException;
use App\Core\Localization\Exceptions\LanguageNotFoundException;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Modules\Settings\Application\Http\Requests\DeleteLanguageRequest;
use Modules\Settings\Application\Http\Requests\UpdateSettingGroupRequest;
use Modules\Settings\Application\Http\Requests\UploadLanguagePackageRequest;
use Modules\Settings\Domain\Contracts\SettingRepositoryInterface;
use Modules\Settings\Domain\Services\SettingService;
use Throwable;

final class SettingController extends Controller
{
    public function __construct(
        private readonly SettingService $settings,
        private readonly SettingRepositoryInterface $settingRepository,
        private readonly LanguageRegistryInterface $languages,
        private readonly LanguageRepositoryInterface $languageRepository,
        private readonly LanguageArchiveInstallerInterface $languageArchiveInstaller,
        private readonly LanguageDeleterInterface $languageDeleter,
    ) {
    }

    public function index(): View
    {
        return view('settings::settings.index', [
            'groups' => $this->settings->getGroupsWithSettings(),
        ]);
    }

    public function editGroup(string $group): View
    {
        $settings = $this->settings->getEditableGroup($group);

        $currentDefaultLocale = (string) (
            $settings->firstWhere('key', 'system.default_locale')?->value
            ?? $this->languages->getFallbackLocale()
        );

        return view('settings::settings.group', [
            'group' => $group,
            'settings' => $settings,
            'availableLocales' => $this->languages->getDropdownOptions(),
            'manageableLanguages' => $group === 'system'
                ? $this->languageRepository->getAll()
                : collect(),
            'currentDefaultLocale' => $currentDefaultLocale,
        ]);
    }

    public function updateGroup(UpdateSettingGroupRequest $request, string $group): RedirectResponse
    {
        $payload = $request->validatedPayload();
        $payload['group'] = $group;

        $this->validateLocaleSetting($payload);

        $runtimeSettings = $this->settingRepository->getRuntimeSettings();

        $previousDefaultLocale = (string) $runtimeSettings->get(
            'system.default_locale',
            $this->languages->getFallbackLocale()
        );

        $this->settings->updateGroup($payload);

        $newDefaultLocale = $this->extractNewDefaultLocale($payload);

        if (
            $group === 'system'
            && $newDefaultLocale !== null
            && $previousDefaultLocale !== $newDefaultLocale
        ) {
            $request->session()->put('locale', $newDefaultLocale);

            event(new DefaultLocaleChangedEvent(
                oldLocale: $previousDefaultLocale,
                newLocale: $newDefaultLocale,
            ));
        }

        return redirect()
            ->route('settings.group.edit', ['group' => $group])
            ->with('success', __('settings::settings.updated'));
    }

    public function uploadLanguage(UploadLanguagePackageRequest $request): RedirectResponse
    {
        $filename = 'language_' . now()->format('Ymd_His') . '_' . bin2hex(random_bytes(8)) . '.zip';

        $storedArchive = $request->file('language_package')->storeAs(
            'tmp/languages',
            $filename,
            'local'
        );

        $absoluteArchivePath = Storage::disk('local')->path($storedArchive);

        try {
            $language = $this->languageArchiveInstaller->installFromArchive($absoluteArchivePath);

            event(new LanguageInstalledEvent($language));

            return redirect()
                ->route('settings.group.edit', ['group' => 'system'])
                ->with('success', __('core-localization::ui.language_installed_success'));
        } catch (LanguageAlreadyExistsException|InvalidLanguageManifestException $exception) {
            return redirect()
                ->route('settings.group.edit', ['group' => 'system'])
                ->withErrors([
                    'language_package' => $exception->getMessage(),
                ]);
        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->route('settings.group.edit', ['group' => 'system'])
                ->withErrors([
                    'language_package' => app()->isLocal()
                        ? $exception->getMessage()
                        : __('core-localization::ui.language_install_failed'),
                ]);
        } finally {
            Storage::disk('local')->delete($storedArchive);
        }
    }

    public function destroyLanguage(DeleteLanguageRequest $request, string $code): RedirectResponse
    {
        $language = $this->languageRepository->findByCode($code);

        try {
            $runtimeSettings = $this->settingRepository->getRuntimeSettings();

            $currentDefaultLocale = (string) $runtimeSettings->get(
                'system.default_locale',
                $this->languages->getFallbackLocale()
            );

            $this->languageDeleter->deleteByCode($code, $currentDefaultLocale);

            event(new LanguageDeletedEvent(
                code: $code,
                language: $language,
            ));

            return redirect()
                ->route('settings.group.edit', ['group' => 'system'])
                ->with('success', __('core-localization::ui.language_deleted_success'));
        } catch (LanguageDeletionException|LanguageNotFoundException $exception) {
            return redirect()
                ->route('settings.group.edit', ['group' => 'system'])
                ->withErrors([
                    'language_management' => $exception->getMessage(),
                ]);
        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->route('settings.group.edit', ['group' => 'system'])
                ->withErrors([
                    'language_management' => app()->isLocal()
                        ? $exception->getMessage()
                        : __('core-localization::ui.language_delete_failed'),
                ]);
        }
    }

    private function validateLocaleSetting(array $payload): void
    {
        if (($payload['group'] ?? '') !== 'system') {
            return;
        }

        if (!array_key_exists('system.default_locale', $payload['values'])) {
            return;
        }

        $locale = is_scalar($payload['values']['system.default_locale'])
            ? (string) $payload['values']['system.default_locale']
            : null;

        if (!$this->languages->isSupported($locale)) {
            throw ValidationException::withMessages([
                'values.system.default_locale' => __('core-localization::ui.unsupported_locale'),
            ]);
        }
    }

    private function extractNewDefaultLocale(array $payload): ?string
    {
        if (($payload['group'] ?? '') !== 'system') {
            return null;
        }

        if (!array_key_exists('system.default_locale', $payload['values'])) {
            return null;
        }

        if (!is_scalar($payload['values']['system.default_locale'])) {
            return null;
        }

        return $this->languages->normalize(
            (string) $payload['values']['system.default_locale']
        );
    }
}