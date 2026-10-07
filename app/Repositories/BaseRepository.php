<?php
namespace App\Repositories;

use App\Contracts\Repositories\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->paginate($perPage);
    }

    public function find(int $id): ?Model
    {
        return $this->model->find($id);
    }

    public function findBy(string $field, mixed $value): ?Model
    {
        return $this->model->where($field, $value)->first();
    }

    public function findWhere(string $field, mixed $value): Collection
    {
        return $this->model->where($field, $value)->get();
    }

    public function create(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            return $this->model->create($data);
        });
    }

    public function update(int $id, array $data): Model
    {
        return DB::transaction(function () use ($id, $data) {
            $record = $this->model->findOrFail($id);
            $record->update($data);
            return $record->fresh();
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $record = $this->model->findOrFail($id);
            return $record->delete();
        });
    }

    public function forceDelete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $record = $this->model->withTrashed()->findOrFail($id);
            return $record->forceDelete();
        });
    }

    public function restore(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $record = $this->model->withTrashed()->findOrFail($id);
            return $record->restore();
        });
    }

    public function withTrashed(): self
    {
        $this->model = $this->model->withTrashed();
        return $this;
    }

    public function onlyTrashed(): self
    {
        $this->model = $this->model->onlyTrashed();
        return $this;
    }
}
