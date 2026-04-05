<?php

declare(strict_types=1);

namespace App\Core\Security\Authentication;

use Laravel\Sanctum\PersonalAccessToken;

final class TokenGuard
{
    public function validate(string $token): ?int
    {
        $access = PersonalAccessToken::findToken($token);

        if (!$access) {
            return null;
        }

        return $access->tokenable_id;
    }
}