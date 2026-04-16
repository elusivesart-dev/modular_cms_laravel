<?php

declare(strict_types=1);

namespace App\Core\Localization\Http\Controllers;

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
use App\Core\Localization\Http\Requests\DeleteLanguageRequest;
use App\Core\Localization\Http\Requests\UpdateDefaultLocaleRequest;
use App\Core\Localization\Http\Requests\UploadLanguagePackageRequest;
use App\Core\Settings\Contracts\SystemSettingsStoreInterface;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Throwable;

final class LanguageController extends Controller
{
    public function __construct(
        private readonly LanguageRegistryInterface $languages,
        private readonly LanguageRepositoryInterface $languageRepository,
        private readonly LanguageArchiveInstallerInterface $languageArchiveInstaller,
        private readonly LanguageDeleterInterface $languageDeleter,
        private readonly SystemSettingsStoreInterface $settings,
    ) {
    }

    public function index(): View
    {
        $currentDefaultLocale = (string) $this->settings->get(
            'system.default_locale',
            $this->languages->getFallbackLocale()
        );

        return view('core-localization::languages.index', [
            'availableLocales' => $this->languages->getDropdownOptions(),
            'languages' => $this->languageRepository->getAll(),
            'currentDefaultLocale' => $currentDefaultLocale,
        ]);
    }

    public function updateDefaultLocale(UpdateDefaultLocaleRequest $request): RedirectResponse
    {
        $payload = $request->validatedPayload();

        $previousDefaultLocale = (string) $this->settings->get(
            'system.default_locale',
            $this->languages->getFallbackLocale()
        );

        $this->settings->putString(
            group: 'system',
            key: 'system.default_locale',
            value: $payload['locale'],
            label: null,
            description: null,
            isPublic: false,
            isSystem: true,
        );

        $request->session()->put('locale', $payload['locale']);

        if ($previousDefaultLocale !== $payload['locale']) {
            event(new DefaultLocaleChangedEvent(
                oldLocale: $previousDefaultLocale,
                newLocale: $payload['locale'],
            ));
        }

        return redirect()
            ->route('localization.languages.index')
            ->with('success', __('core-localization::ui.default_locale_updated_success'));
    }

    public function upload(UploadLanguagePackageRequest $request): RedirectResponse
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
                ->route('localization.languages.index')
                ->with('success', __('core-localization::ui.language_installed_success'));
        } catch (LanguageAlreadyExistsException|InvalidLanguageManifestException $exception) {
            return redirect()
                ->route('localization.languages.index')
                ->withErrors([
                    'language_package' => $exception->getMessage(),
                ]);
        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->route('localization.languages.index')
                ->withErrors([
                    'language_package' => app()->isLocal()
                        ? $exception->getMessage()
                        : __('core-localization::ui.language_install_failed'),
                ]);
        } finally {
            Storage::disk('local')->delete($storedArchive);
        }
    }

    public function destroy(DeleteLanguageRequest $request, string $code): RedirectResponse
    {
        $language = $this->languageRepository->findByCode($code);

        try {
            $currentDefaultLocale = (string) $this->settings->get(
                'system.default_locale',
                $this->languages->getFallbackLocale()
            );

            $this->languageDeleter->deleteByCode($code, $currentDefaultLocale);

            event(new LanguageDeletedEvent(
                code: $code,
                language: $language,
            ));

            return redirect()
                ->route('localization.languages.index')
                ->with('success', __('core-localization::ui.language_deleted_success'));
        } catch (LanguageDeletionException|LanguageNotFoundException $exception) {
            return redirect()
                ->route('localization.languages.index')
                ->withErrors([
                    'language_management' => $exception->getMessage(),
                ]);
        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->route('localization.languages.index')
                ->withErrors([
                    'language_management' => app()->isLocal()
                        ? $exception->getMessage()
                        : __('core-localization::ui.language_delete_failed'),
                ]);
        }
    }
}