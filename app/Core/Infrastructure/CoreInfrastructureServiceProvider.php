<?php

declare(strict_types=1);

namespace App\Core\Infrastructure;

use App\Core\Database\Contracts\TransactionManagerInterface;
use App\Core\Database\Transactions\DatabaseTransactionManager;
use Illuminate\Support\ServiceProvider;

final class CoreInfrastructureServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TransactionManagerInterface::class, DatabaseTransactionManager::class);
        $this->app->singleton(DatabaseTransactionManager::class);
    }

    public function boot(): void
    {
    }
}
