<?php

declare(strict_types=1);

namespace App\Core\Events\Hooks;

final class HookManager
{
    private array $hooks = [];

    public function register(string $hook, callable $callback): void
    {
        $this->hooks[$hook][] = $callback;
    }

    public function run(string $hook, mixed $value = null): mixed
    {
        if (!isset($this->hooks[$hook])) {
            return $value;
        }

        foreach ($this->hooks[$hook] as $callback) {
            $value = $callback($value);
        }

        return $value;
    }
}