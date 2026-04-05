<?php

declare(strict_types=1);

namespace Modules\Audit\Application\Providers;

use App\Core\Audit\Models\AuditLog;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Modules\Audit\Application\Policies\AuditLogPolicy;
use Modules\Audit\Application\Support\AuditLogFormatter;
use Modules\Audit\Domain\Contracts\AuditLogRepositoryInterface;
use Modules\Audit\Infrastructure\Repositories\EloquentAuditLogRepository;
use Modules\Audit\Listeners\RecordPermissionAuditTrail;
use Modules\Audit\Listeners\RecordRoleAuditTrail;
use Modules\Audit\Listeners\RecordSettingAuditTrail;
use Modules\Audit\Listeners\RecordUserAuditTrail;
use Modules\Permissions\Domain\Events\PermissionCreatedEvent;
use Modules\Permissions\Domain\Events\PermissionDeletedEvent;
use Modules\Permissions\Domain\Events\PermissionsSyncedToRoleEvent;
use Modules\Permissions\Domain\Events\PermissionUpdatedEvent;
use Modules\Roles\Domain\Events\RoleAssignedEvent;
use Modules\Roles\Domain\Events\RoleCreatedEvent;
use Modules\Roles\Domain\Events\RoleDeletedEvent;
use Modules\Roles\Domain\Events\RoleRevokedEvent;
use Modules\Roles\Domain\Events\RoleUpdatedEvent;
use Modules\Settings\Domain\Events\SettingCreatedEvent;
use Modules\Settings\Domain\Events\SettingDeletedEvent;
use Modules\Settings\Domain\Events\SettingsGroupUpdatedEvent;
use Modules\Settings\Domain\Events\SettingUpdatedEvent;
use Modules\Users\Domain\Events\UserCreatedEvent;
use Modules\Users\Domain\Events\UserDeletedEvent;
use Modules\Users\Domain\Events\UserUpdatedEvent;

final class AuditServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AuditLogRepositoryInterface::class, EloquentAuditLogRepository::class);
        $this->app->singleton(AuditLogFormatter::class);
    }

    public function boot(): void
    {
        Gate::policy(AuditLog::class, AuditLogPolicy::class);

        $this->registerAuditEventListeners();

        $this->loadViewsFrom(base_path('Modules/Audit/UI/Resources/views'), 'audit');
        $this->loadTranslationsFrom(base_path('Modules/Audit/UI/Resources/lang'), 'audit');

        Route::middleware(['web', 'auth'])
            ->group(base_path('Modules/Audit/Routes/web.php'));
    }

    private function registerAuditEventListeners(): void
    {
        Event::listen(RoleAssignedEvent::class, [RecordRoleAuditTrail::class, 'handleRoleAssigned']);
        Event::listen(RoleRevokedEvent::class, [RecordRoleAuditTrail::class, 'handleRoleRevoked']);
        Event::listen(RoleCreatedEvent::class, [RecordRoleAuditTrail::class, 'handleRoleCreated']);
        Event::listen(RoleUpdatedEvent::class, [RecordRoleAuditTrail::class, 'handleRoleUpdated']);
        Event::listen(RoleDeletedEvent::class, [RecordRoleAuditTrail::class, 'handleRoleDeleted']);

        Event::listen(PermissionCreatedEvent::class, [RecordPermissionAuditTrail::class, 'handlePermissionCreated']);
        Event::listen(PermissionUpdatedEvent::class, [RecordPermissionAuditTrail::class, 'handlePermissionUpdated']);
        Event::listen(PermissionDeletedEvent::class, [RecordPermissionAuditTrail::class, 'handlePermissionDeleted']);
        Event::listen(PermissionsSyncedToRoleEvent::class, [RecordPermissionAuditTrail::class, 'handlePermissionsSyncedToRole']);

        Event::listen(UserCreatedEvent::class, [RecordUserAuditTrail::class, 'handleUserCreated']);
        Event::listen(UserUpdatedEvent::class, [RecordUserAuditTrail::class, 'handleUserUpdated']);
        Event::listen(UserDeletedEvent::class, [RecordUserAuditTrail::class, 'handleUserDeleted']);

        Event::listen(SettingCreatedEvent::class, [RecordSettingAuditTrail::class, 'handleSettingCreated']);
        Event::listen(SettingUpdatedEvent::class, [RecordSettingAuditTrail::class, 'handleSettingUpdated']);
        Event::listen(SettingDeletedEvent::class, [RecordSettingAuditTrail::class, 'handleSettingDeleted']);
        Event::listen(SettingsGroupUpdatedEvent::class, [RecordSettingAuditTrail::class, 'handleSettingsGroupUpdated']);
    }
}