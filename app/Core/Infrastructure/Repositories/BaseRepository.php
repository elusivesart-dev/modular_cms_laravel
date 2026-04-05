<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Repositories;

use App\Core\Infrastructure\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository implements RepositoryInterface
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function find(int|string $id): mixed
    {
        return $this->model->findOrFail($id);
    }

    public function all(): iterable
    {
        return $this->model->all();
    }

    public function create(array $data): mixed
    {
        return $this->model->create($data);
    }

    public function update(int|string $id, array $data): mixed
    {
        $model = $this->find($id);
        $model->update($data);

        return $model;
    }

    public function delete(int|string $id): bool
    {
        return (bool) $this->find($id)->delete();
    }
}