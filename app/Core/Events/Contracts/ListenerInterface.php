<?php

declare(strict_types=1);

namespace App\Core\Events\Contracts;

interface ListenerInterface
{
    public function handle(EventInterface $event): void;
}