<?php

declare(strict_types=1);

namespace App\Core\Exceptions;

use Throwable;
use Illuminate\Support\Facades\Log;

final class ExceptionHandler
{
    public function report(Throwable $e): void
    {
        Log::error($e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);
    }
}