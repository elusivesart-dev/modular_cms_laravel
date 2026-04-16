<?php

declare(strict_types=1);

namespace Modules\Users\Application\Services;

use App\Core\Database\Contracts\TransactionManagerInterface;
use App\Core\Media\Contracts\MediaAssetManagerInterface;
use DomainException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Users\Application\Contracts\UserProfileWorkflowInterface;
use Modules\Users\Application\Contracts\UserServiceInterface;
use Modules\Users\Domain\Contracts\UserEntityInterface;
use Modules\Users\Domain\Contracts\UserRepositoryInterface;
use Modules\Users\Domain\DTOs\CreateUserData;
use Modules\Users\Infrastructure\Models\User;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class UserProfileWorkflowService implements UserProfileWorkflowInterface
{
    public function __construct(
        private readonly UserServiceInterface $users,
        private readonly UserRepositoryInterface $userRepository,
        private readonly MediaAssetManagerInterface $mediaAssets,
        private readonly TransactionManagerInterface $transactions,
    ) {
    }

    public function register(array $payload): UserEntityInterface
    {
        return $this->transactions->transaction(function () use ($payload): UserEntityInterface {
            $user = $this->users->create(new CreateUserData(
                name: (string) $payload['name'],
                email: (string) $payload['email'],
                password: (string) $payload['password'],
                isActive: true,
            ));

            $updated = $this->users->update($user, [
                'slug' => $this->makeUniqueSlug((string) $payload['name'], (int) $user->getKey()),
            ]);

            $updated->sendEmailVerificationNotification();

            return $updated;
        });
    }

    public function updateProfile(
        UserEntityInterface $user,
        array $payload,
        ?UploadedFile $avatar = null,
        ?int $uploadedBy = null
    ): UserEntityInterface {
        return $this->transactions->transaction(function () use ($user, $payload, $avatar, $uploadedBy): UserEntityInterface {
            $model = $this->toModel($user);
            $oldEmail = (string) $model->email;

            if ($avatar !== null) {
                $uploadedMedia = $this->mediaAssets->uploadImage(
                    file: $avatar,
                    uploadedBy: $uploadedBy,
                    title: (string) $model->name,
                    altText: (string) $model->name,
                );

                $payload['avatar_media_id'] = $uploadedMedia->id;
                $payload['avatar_path'] = null;

                $this->cleanupLegacyAvatarPath($model);
            }

            $updated = $this->users->update($user, $payload);

            if ($oldEmail !== (string) $payload['email']) {
                $updated = $this->userRepository->clearEmailVerification($updated);
                $updated->sendEmailVerificationNotification();
            }

            return $updated;
        });
    }

    public function verifyEmail(int $userId, string $hash): bool
    {
        $user = $this->userRepository->findById($userId);

        if ($user === null) {
            throw new NotFoundHttpException();
        }

        if (! hash_equals($hash, sha1((string) $user->getEmailForVerification()))) {
            throw new AccessDeniedHttpException();
        }

        if ((bool) $user->hasVerifiedEmail()) {
            return false;
        }

        $updated = $this->userRepository->markEmailAsVerified($user);

        event(new Verified($this->toModel($updated)));

        return true;
    }

    private function makeUniqueSlug(string $name, int $ignoreUserId = 0): string
    {
        $baseSlug = Str::slug($name);
        $baseSlug = $baseSlug !== '' ? $baseSlug : 'user';
        $slug = $baseSlug;
        $suffix = 1;

        while ($this->userRepository->slugExists($slug, $ignoreUserId)) {
            $slug = $baseSlug . '-' . $suffix;
            $suffix++;
        }

        return $slug;
    }

    private function cleanupLegacyAvatarPath(User $user): void
    {
        if (! empty($user->avatar_path) && Storage::disk('public')->exists((string) $user->avatar_path)) {
            Storage::disk('public')->delete((string) $user->avatar_path);
        }
    }

    private function toModel(UserEntityInterface $user): User
    {
        if (! $user instanceof User) {
            throw new DomainException('Unsupported user entity implementation.');
        }

        return $user;
    }
}