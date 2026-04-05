<?php

declare(strict_types=1);

use App\Core\Localization\Contracts\LanguageRegistryInterface;
use App\Core\Themes\Support\PublicThemeView;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;
use Modules\Users\Application\Http\Controllers\UserController;

Route::middleware(['web'])->group(static function (): void {
    Route::middleware('guest')->group(static function (): void {
        Route::get('/register', [UserController::class, 'showRegisterForm'])
            ->name('register');

        Route::post('/register', [UserController::class, 'register'])
            ->middleware('throttle:5,1')
            ->name('register.store');
    });

    Route::get('/email/verify/{id}/{hash}', [UserController::class, 'verifyEmail'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::get('/locale/{locale}', static function (string $locale): RedirectResponse {
        /** @var LanguageRegistryInterface $languageRegistry */
        $languageRegistry = app(LanguageRegistryInterface::class);

        $normalizedLocale = $languageRegistry->normalize($locale);

        abort_if($normalizedLocale === null, 404);

        session()->put('locale', $normalizedLocale);
        session()->save();

        return redirect()->back();
    })->name('locale.switch');

    Route::middleware(['auth', 'throttle:30,1'])->group(static function (): void {
        Route::get('/profile', [UserController::class, 'showMyProfile'])
            ->name('profile.me');

        Route::get('/profile/edit', [UserController::class, 'editMyProfile'])
            ->name('profile.edit');

        Route::put('/profile', [UserController::class, 'updateMyProfile'])
            ->middleware('throttle:10,1')
            ->name('profile.update');
    });

    Route::get('/users/{user:slug}', [UserController::class, 'showPublic'])
        ->name('profile.show');

    Route::get('/', static function () {
        return view(PublicThemeView::make('home.index'));
    })->name('home');
});