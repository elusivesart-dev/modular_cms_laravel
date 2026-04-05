<?php

declare(strict_types=1);

namespace App\Core\Database\Contracts;

interface TransactionManagerInterface
{
    public function transaction(callable $callback): mixed;
}
