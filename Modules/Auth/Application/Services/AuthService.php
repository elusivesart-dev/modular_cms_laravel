<?php

declare(strict_types=1);

namespace Modules\Auth\Application\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Modules\Auth\Application\Contracts\AuthServiceInterface;
use Modules\Auth\Domain\DTOs\LoginData;
use Modules\Auth\Domain\Exceptions\AuthenticationFailedException;
use Modules\Users\Infrastructure\Models\User;

final class AuthService implements AuthServiceInterface
{
    public function attempt(LoginData $data, Request $request): void
    {
        $key = $this->throttleKey($request, $data->email);

        if (RateLimiter::tooManyAttempts(
            $key,
            (int) config('auth-module.login_throttle.max_attempts', 5)
        )) {
            throw AuthenticationFailedException::invalidCredentials();
        }

        /** @var User|null $user */
        $user = User::query()
            ->where('email', $data->email)
            ->first();

        if (
            $user === null ||
            !$user->is_active ||
            !Hash::check($data->password, $user->password)
        ) {
            RateLimiter::hit($key, (int) config('auth-module.login_throttle.decay_seconds', 60));

            throw AuthenticationFailedException::invalidCredentials();
        }

        if (!$user->hasVerifiedEmail()) {
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