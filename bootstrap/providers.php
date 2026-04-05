<?php

use App\Core\Events\Providers\EventServiceProvider as CoreEventServiceProvider;
use App\Core\Infrastructure\CoreInfrastructureServiceProvider;
use App\Core\Installer\Providers\InstallerServiceProvider;
use App\Core\Modules\ModuleServiceProvider;
use App\Core\RBAC\Providers\RBACServiceProvider;
use App\Core\Security\Providers\SecurityServiceProvider;
use App\Core\Themes\Providers\ThemeServiceProvider;
use App\Providers\AppServiceProvider;
use App\Providers\CoreServiceProvider;
use App\Core\Localization\Providers\LocalizationServiceProvider;
use Modules\Audit\Application\Providers\AuditServiceProvider;
use Modules\Permissions\PermissionsServiceProvider;


return [
    AppServiceProvider::class,
    CoreServiceProvider::class,
    CoreInfrastructureServiceProvider::class,
    ModuleServiceProvider::class,
    RBACServiceProvider::class,
    CoreEventServiceProvider::class,
    SecurityServiceProvider::class,
    ThemeServiceProvider::class,
    InstallerServiceProvider::class,
    PermissionsServiceProvider::class,
    AuditServiceProvider::class,
	LocalizationServiceProvider::class,
];