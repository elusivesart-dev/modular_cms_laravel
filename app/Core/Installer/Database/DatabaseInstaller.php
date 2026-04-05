<?php

declare(strict_types=1);

namespace App\Core\Installer\Database;

use Illuminate\Database\DatabaseManager;
use RuntimeException;
use Throwable;

final class DatabaseInstaller
{
    public function __construct(
        private readonly DatabaseManager $database
    ) {
    }

    public function checkConnection(): void
    {
        try {
            $connection = $this->database->connection();
            $connection->getPdo();
            $connection->select('SELECT 1');
        } catch (Throwable $exception) {
            throw new RuntimeException('Database connection failed.', previous: $exception);
        }
    }
}