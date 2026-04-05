<?php

declare(strict_types=1);

namespace Modules\Auth\Application\Contracts;

use Illuminate\Http\Request;
use Modules\Auth\Domain\DTOs\LoginData;

interface AuthServiceInterface
{
    public function attempt(LoginData $data, Request $request): void;

    public function logout(Request $request): void;
}