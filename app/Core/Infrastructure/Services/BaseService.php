<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Services;

use App\Core\Infrastructure\Contracts\RepositoryInterface;
use App\Core\Infrastructure\Contracts\ServiceInterface;

abstract class BaseService implements ServiceInterface
{
    public function __construct(
        protected RepositoryInterface $repository
    ) {}

    public function find(int|string $id): mixed
    {
        return $this->repository->find($id);
    }

    public function all(): iterable
    {
        return $this->repository->all();
    }

    public function create(array $data): mixed
    {
        return $this->repository->create($data);
    }

    public function update(int|string $id, array $data): mixed
    {
        return $this->repository->update($id, $data);
    }

    public function delete(int|string $id): bool
    {
        return $this->repository->delete($id);
    }
}