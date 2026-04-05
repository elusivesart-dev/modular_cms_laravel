<?php

declare(strict_types=1);

namespace Modules\Users\Application\Http\Controllers;

use App\Core\RBAC\Contracts\RoleCatalogInterface;
use App\Core\RBAC\Contracts\RoleManagerInterface;
use App\Core\RBAC\Exceptions\RoleOperationException;
use App\Http\Controllers\Controller;
use DomainException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Media\Application\Contracts\MediaServiceInterface;
use Modules\Users\Application\Contracts\UserServiceInterface;
use Modules\Users\Application\Http\Requests\PublicRegisterUserRequest;
use Modules\Users\Application\Http\Requests\PublicUpdateProfileRequest;
use Modules\Users\Application\Http\Requests\StoreUserRequest;
use Modules\Users\Application\Http\Requests\UpdateUserRequest;
use Modules\Users\Domain\DTOs\CreateUserData;
use Modules\Users\Infrastructure\Models\User;

final class UserController extends Controller
{
    public function __construct(
        private readonly UserServiceInterface $users,
        private readonly RoleManagerInterface $roles,
        private readonly RoleCatalogInterface $roleCatalog,
        private readonly MediaServiceInterface $media,
    ) {
        $this->authorizeResource(User::class, 'user');
    }

    public function index(): View
    {
        return view('users::users.index', [
            'users' => $this->users->paginate(),
        ]);
    }

    public function create(): View
    {
        return view('users::users.create', [
            'roles' => $this->roleCatalog->listForSelection(),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $payload = $request->validatedPayload();

        try {
            DB::transaction(function () use ($payload, &$user): void {
                $user = $this->users->create(new CreateUserData(
                    name: (string) $payload['name'],
                    email: (string) $payload['email'],
                    password: (string) $payload['password'],
                    isActive: (bool) $payload['is_active'],
                ));

                $this->roles->syncRolesToSubject(
                    $payload['role_slugs'] ?? [],
                    User::class,
                    (int) $user->getKey(),
                );
            });
        } catch (RoleOperationException $exception) {
            return back()
                ->withInput()
                ->withErrors([
                    'role_slugs' => $exception->getMessage(),
                ]);
        }

        return redirect()
            ->route('users.index')
            ->with('success', __('users::users.created'));
    }

    public function show(User $user): View
    {
        $roles = $this->roles->rolesForSubject(
            User::class,
            $user->getKey()
        );

        return view('users::users.show', [
            'user' => $user,
            'roles' => $roles,
        ]);
    }

    public function edit(User $user): View
    {
        $roles = $this->roles->rolesForSubject(
            User::class,
            $user->getKey()
        );

        return view('users::users.edit', [
            'user' => $user->load('avatarMedia'),
            'roles' => $this->roleCatalog->listForSelection(),
            'selectedRoleSlugs' => $roles->pluck('slug')->all(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $payload = $request->validatedPayload();

        try {
            DB::transaction(function () use ($user, $payload, $request): void {
                if ($request->hasFile('avatar')) {
                    $uploadedMedia = $this->media->upload(
                        file: $request->file('avatar'),
                        uploadedBy: $request->user() !== null ? (int) $request->user()->getAuthIdentifier() : null,
                        title: (string) $user->name,
                        altText: (string) $user->name,
                    );

                    $payload['avatar_media_id'] = (int) $uploadedMedia->getKey();
                    $payload['avatar_path'] = null;

                    $this->cleanupLegacyAvatarPath($user);
                } elseif (! empty($payload['avatar_media_id'])) {
                    $payload['avatar_path'] = null;

                    $this->cleanupLegacyAvatarPath($user);
                }

                $this->users->update($user, $payload);

                $this->roles->syncRolesToSubject(
                    $payload['role_slugs'] ?? [],
                    User::class,
                    (int) $user->getKey(),
                );
            });
        } catch (RoleOperationException $exception) {
            return back()
                ->withInput()
                ->withErrors([
                    'role_slugs' => $exception->getMessage(),
                ]);
        }

        return redirect()
            ->route('users.index')
            ->with('success', __('users::users.updated'));
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        $actor = auth()->user();

        if ($actor !== null && method_exists($actor, 'getAuthIdentifier')) {
            if ((int) $actor->getAuthIdentifier() === (int) $user->getKey()) {
                return redirect()
                    ->route('users.index')
                    ->with('error', __('users::users.exceptions.cannot_delete_self'));
            }
        }

        try {
            $this->users->delete($user);
        } catch (DomainException $exception) {
            return redirect()
                ->route('users.index')
                ->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('users.index')
            ->with('success', __('users::users.deleted'));
    }

    public function showRegisterForm(): View
    {
        return view('users::users.public.register', [
            'captchaSiteKey' => (string) config('auth-module.registration.captcha.site_key', ''),
            'captchaEnabled' => (bool) config('auth-module.registration.captcha.enabled', false),
            'captchaAction' => (string) config('auth-module.registration.captcha.action', 'register'),
        ]);
    }

    public function register(PublicRegisterUserRequest $request): RedirectResponse
    {
        $payload = $request->validatedPayload();

        $user = $this->users->create(new CreateUserData(
            name: $payload['name'],
            email: $payload['email'],
            password: $payload['password'],
            isActive: true,
        ));

        $slug = $this->makeUniqueSlug($payload['name'], (int) $user->getKey());

        $this->users->update($user, [
            'slug' => $slug,
        ]);

        $user->sendEmailVerificationNotification();

        return redirect()
            ->route('login')
            ->with('success', __('users::users.public.registration_success'));
    }

    public function showPublic(User $user): View
    {
        abort_unless($user->is_active, 404);

        return view('users::users.public.show', [
            'profileUser' => $user->load('avatarMedia'),
            'isOwnProfile' => auth()->check() && (int) auth()->id() === (int) $user->getKey(),
        ]);
    }

    public function showMyProfile(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        return view('users::users.public.show', [
            'profileUser' => $user->load('avatarMedia'),
            'isOwnProfile' => true,
        ]);
    }

    public function editMyProfile(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        return view('users::users.public.edit', [
            'user' => $user->load('avatarMedia'),
        ]);
    }

    public function updateMyProfile(PublicUpdateProfileRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $payload = $request->validatedPayload();
        $oldEmail = $user->email;

        if ($request->hasFile('avatar')) {
            $uploadedMedia = $this->media->upload(
                file: $request->file('avatar'),
                uploadedBy: $request->user() !== null ? (int) $request->user()->getAuthIdentifier() : null,
                title: (string) $user->name,
                altText: (string) $user->name,
            );

            $payload['avatar_media_id'] = (int) $uploadedMedia->getKey();
            $payload['avatar_path'] = null;

            $this->cleanupLegacyAvatarPath($user);
        }

        $this->users->update($user, $payload);

        if ($oldEmail !== $payload['email']) {
            $user->forceFill([
                'email_verified_at' => null,
            ])->save();

            $user->sendEmailVerificationNotification();
        }

        return redirect()
            ->route('profile.me')
            ->with('success', __('users::users.public.profile_updated'));
    }

    public function verifyEmail(Request $request): RedirectResponse
    {
        abort_unless($request->hasValidSignature(), 403);

        /** @var User $user */
        $user = User::query()->findOrFail((int) $request->route('id'));

        abort_unless(
            hash_equals(
                (string) $request->route('hash'),
                sha1($user->getEmailForVerification())
            ),
            403
        );

        if ($user->hasVerifiedEmail()) {
            return redirect()
                ->route('login')
                ->with('info', __('users::users.public.email_already_verified'));
        }

        $user->markEmailAsVerified();
        event(new Verified($user));

        return redirect()
            ->route('login')
            ->with('success', __('users::users.public.email_verified'));
    }

    private function makeUniqueSlug(string $name, int $ignoreUserId = 0): string
    {
        $baseSlug = Str::slug($name);
        $baseSlug = $baseSlug !== '' ? $baseSlug : 'user';
        $slug = $baseSlug;
        $suffix = 1;

        while (
            User::query()
                ->where('slug', $slug)
                ->when($ignoreUserId > 0, static function ($query) use ($ignoreUserId) {
                    $query->where('id', '!=', $ignoreUserId);
                })
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $suffix;
            $suffix++;
        }

        return $slug;
    }

    private function cleanupLegacyAvatarPath(User $user): void
    {
        if (! empty($user->avatar_path) && Storage::disk('public')->exists($user->avatar_path)) {
            Storage::disk('public')->delete($user->avatar_path);
        }
    }
}
