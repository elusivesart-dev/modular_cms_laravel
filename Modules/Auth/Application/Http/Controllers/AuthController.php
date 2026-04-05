<?php

declare(strict_types=1);

namespace Modules\Auth\Application\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Auth\Application\Contracts\AuthServiceInterface;
use Modules\Auth\Application\Http\Requests\LoginRequest;
use Modules\Auth\Domain\Exceptions\AuthenticationFailedException;

final class AuthController extends Controller
{
    public function __construct(
        private readonly AuthServiceInterface $auth,
    ) {
    }

    public function showLoginForm(): View
    {
        return view('auth-module::auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        try {
            $this->auth->attempt($request->loginData(), $request);
        } catch (AuthenticationFailedException $exception) {
            return back()
                ->withErrors([
                    'email' => $exception->getMessage(),
                ])
                ->onlyInput('email');
        }

        return redirect()->intended(route(config('auth-module.redirect_after_login', 'users.index')));
    }

    public function logout(Request $request): RedirectResponse
    {
        $this->auth->logout($request);

        return redirect()->route('login');
    }
}