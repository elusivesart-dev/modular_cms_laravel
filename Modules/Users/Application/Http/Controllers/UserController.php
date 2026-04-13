<?php

declare(strict_types=1);

namespace Modules\Users\Application\Http\Controllers;

use App\Core\RBAC\Exceptions\RoleOperationException;
use App\Http\Controllers\Controller;
use DomainException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Users\Application\Contracts\UserAdministrationWorkflowInterface;
use Modules\Users\Application\Contracts\UserProfileWorkflowInterface;
use Modules\Users\Application\Contracts\UserServiceInterface;
use Modules\Users\Application\Http\Requests\PublicRegisterUserRequest;
use Modules\Users\Application\Http\Requests\PublicUpdateProfileRequest;
use Modules\Users\Application\Http\Requests\StoreUserRequest;
use Modules\Users\Application\Http\Requests\UpdateUserRequest;
use Modules\Users\Infrastructure\Models\User;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class UserController extends Controller
{
    public function __construct(
        private readonly UserServiceInterface $users,
        private readonly UserAdministrationWorkflowInterface $administrationWorkflow,
        private readonly UserProfileWorkflowInterface $profileWorkflow,
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
            'roles' => $this->administrationWorkflow->availableRoles(),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $payload = $request->validatedPayload();

        try {
            $this->administrationWorkflow->store($payload);
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
        return view('users::users.show', [
            'user' => $user,
            'roles' => $this->administrationWorkflow->assignedRoles($user),
        ]);
    }

    public function edit(User $user): View
    {
        return view('users::users.edit', [
            'user' => $user->load('avatarMedia'),
            'roles' => $this->administrationWorkflow->availableRoles(),
            'selectedRoleSlugs' => $this->administrationWorkflow->selectedRoleSlugs($user),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $payload = $request->validatedPayload();

        try {
            $this->administrationWorkflow->update(
                user: $user,
                payload: $payload,
                avatar: $request->file('avatar'),
                uploadedBy: $request->user() !== null ? (int) $request->user()->getAuthIdentifier() : null,
            );
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

        $this->profileWorkflow->register($payload);

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

        $this->profileWorkflow->updateProfile(
            user: $user,
            payload: $payload,
            avatar: $request->file('avatar'),
            uploadedBy: $request->user() !== null ? (int) $request->user()->getAuthIdentifier() : null,
        );

        return redirect()
            ->route('profile.me')
            ->with('success', __('users::users.public.profile_updated'));
    }

    public function verifyEmail(Request $request): RedirectResponse
    {
        abort_unless($request->hasValidSignature(), 403);

        try {
            $verified = $this->profileWorkflow->verifyEmail(
                userId: (int) $request->route('id'),
                hash: (string) $request->route('hash'),
            );
        } catch (NotFoundHttpException|AccessDeniedHttpException) {
            abort(403);
        }

        if (! $verified) {
            return redirect()
                ->route('login')
                ->with('info', __('users::users.public.email_already_verified'));
        }

        return redirect()
            ->route('login')
            ->with('success', __('users::users.public.email_verified'));
    }
}