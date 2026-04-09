<?php

declare(strict_types=1);

namespace Modules\Users\Domain\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

interface UserEntityInterface extends Authenticatable, Arrayable, JsonSerializable
{
    public function getKey();

    public function getAuthIdentifier();

    public function getEmailForVerification();

    public function hasVerifiedEmail();

    public function sendEmailVerificationNotification(): void;
}