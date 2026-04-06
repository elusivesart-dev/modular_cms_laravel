<?php

declare(strict_types=1);

namespace App\Core\Database\Contracts;

interface TransactionManagerInterface
{
    /**
     * @template TReturn
     *
     * @param callable(): TReturn $callback
     *
     * @return TReturn
     */
    public function transaction(callable $callback): mixed;
}