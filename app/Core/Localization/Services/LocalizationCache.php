<?php

declare(strict_types=1);

namespace App\Core\Localization\Services;

use Illuminate\Contracts\Cache\Repository as CacheRepository;

final readonly class LocalizationCache
{
    public function __construct(
        private CacheRepository $cache,
    ) {
    }

    public function remember(string $key, callable $callback): mixed
    {
        if (!$this->isEnabled()) {
            return $callback();
        }

        return $this->cache->remember(
            $this->key($key),
            $this->ttl(),
            $callback,
        );
    }

    public function forgetAll(): void
    {
        foreach ($this->knownKeys() as $key) {
            $this->cache->forget($this->key($key));
        }
    }

    public function forget(string $key): void
    {
        $this->cache->forget($this->key($key));
    }

    private function key(string $key): string
    {
        return $this->prefix() . '.' . $key;
    }

    /**
     * @return array<int, string>
     */
    private function knownKeys(): array
    {
        return [
            'languages.all',
            'languages.dropdown',
            'languages.codes',
        ];
    }

    private function isEnabled(): bool
    {
        return (bool) config('localization.cache.enabled', true);
    }

    private function ttl(): int
    {
        return (int) config('localization.cache.ttl', 3600);
    }

    private function prefix(): string
    {
        return (string) config('localization.cache.prefix', 'core.localization');
    }
}