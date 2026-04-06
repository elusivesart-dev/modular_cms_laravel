<?php

declare(strict_types=1);

namespace Modules\Users\Application\Contracts;

use Illuminate\Http\UploadedFile;
use Modules\Users\Infrastructure\Models\User;

interface UserProfileWorkflowInterface
{
    /**
     * @param array{name:string,email:string,password:string} $payload
     */
    public function register(array $payload): User;

    /**
     * @param array{name:string,slug:string,email:string,bio:?string,password?:string} $payload
     */
    public function updateProfile(User $user, array $payload, ?UploadedFile $avatar = null, ?int $uploadedBy = null): User;

    public function verifyEmail(int $userId, string $hash): bool;
}