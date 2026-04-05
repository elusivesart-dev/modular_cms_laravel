<?php

declare(strict_types=1);

namespace App\Core\Database\Transactions;

use App\Core\Database\Contracts\TransactionManagerInterface;
use Illuminate\Support\Facades\DB;

final class DatabaseTransactionManager implements TransactionManagerInterface
{
    public function transaction(callable $callback): mixed
    {
        return DB::transaction($callback);
    }
}
