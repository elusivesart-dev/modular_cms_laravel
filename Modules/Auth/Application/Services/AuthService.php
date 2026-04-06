<?php

declare(strict_types=1);

namespace Modules\Auth\Application\Services;

use App\Core\Auth\Contracts\AuthenticatableUserProviderInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Modules\Auth\Application\Contracts\AuthServiceInterface;
use Modules\Auth\Domain\DTOs\LoginData;
use Modules\Auth\Domain\Exceptions\AuthenticationFailedException;

final class AuthService implements AuthServiceInterface
{
    public function __construct(
        private readonly AuthenticatableUserProviderInterface $users,
    ) {
    }

    public function attempt(LoginData $data, Request $request): void
    {
        $key = $this->throttleKey($request, $data->email);

        if (RateLimiter::tooManyAttempts(
            $key,
            (int) config('auth-module.login_throttle.max_attempts', 5)
        )) {
            throw AuthenticationFailedException::invalidCredentials();
        }

        $user = $this->users->findByEmail($data->email);

        if (
            $user === null ||
            ! $this->users->isActive($user) ||
            ! Hash::check($data->password, $this->users->getPasswordHash($user))
        ) {
            RateLimiter::hit($key, (int) config('auth-module.login_throttle.decay_seconds', 60));

            throw AuthenticationFailedException::invalidCredentials();
        }

        if (! $this->users->hasVerifiedEmail($user)) {
            throw AuthenticationFailedException::emailNotVerified();
        }

        Auth::login($user, $data->remember);

        RateLimiter::clear($key);
        $request->session()->regenerate();
    }

    public function logout(Request $request): void
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    private function throttleKey(Request $request, string $email): string
    {
        return mb_strtolower($email) . '|' . $request->ip();
    }
}