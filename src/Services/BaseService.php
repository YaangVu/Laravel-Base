<?php
/**
 * @Author yaangvu
 * @Date   Aug 06, 2022
 */

namespace YaangVu\LaravelBase\Services;

use Exception;
use Faker\Provider\Uuid;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Traits\Macroable;
use YaangVu\Exceptions\QueryException;
use YaangVu\Exceptions\SystemException;
use YaangVu\LaravelBase\Interfaces\Repository;
use YaangVu\LaravelBase\Interfaces\ShouldCache;
use YaangVu\LaravelBase\Query\Queryable;
use YaangVu\LaravelBase\Traits\Callback;
use YaangVu\LaravelBase\Traits\HasParameter;
use YaangVu\LaravelBase\Traits\Validatable;

abstract class BaseService implements Repository
{
    use Queryable, Callback, Validatable, HasParameter, Macroable;

    public function __construct()
    {
        $this->initModel();
        $this->initAttrs();
    }

    /**
     *  Initial Model for Service
     *
     * @Author      yaangvu
     * @Date        Aug 07, 2022
     *
     */
    abstract function initModel(): void;

    /**
     * @inheritDoc
     */
    public function getAll(bool $paginated = true): LengthAwarePaginator|Collection|array
    {
        if ($this instanceof ShouldCache && Cache::has($cachedKey = $this->model . '-' . Request::serialize()))
            return Cache::get($cachedKey);

        $builder = $this->buildQuery($this->builder);
        try {
            $response = $paginated
                ? $builder->paginate($this->getLimit())
                : $builder->get();

            $this->postGetAll($response);

            return $response;
        } catch (Exception $e) {
            throw new SystemException($e->getMessage() ?? __('laravel-base.system-500'), $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function get(int|string $id): Model
    {
        if ($this instanceof ShouldCache && Cache::has($this->model . "-$id"))
            return Cache::get($this->model . "-$id");

        $entity = $this->relate($this->model->query())->findOrFail($id);

        $this->postGet($id, $entity);

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function add(object $request, bool $transaction = false): Model
    {
        if ($transaction)
            DB::beginTransaction();

        // Validate request before process
        if ($this->validateStoreRequest($request) !== true)
            return $this->model;

        $requestArr = $this->toArray($request);

        foreach ($requestArr as $column => $value) {
            if ($this->fillAbles === ['*']
                || (in_array($column, $this->fillAbles) && !in_array($column, $this->guarded))
            )
                $this->model->{$column} = $this->handleRequestValue($value);
        }

        // If using NoSQL or SQL with table has created_by column then Set created_by is current user
        if (!str_contains($this->driver, 'sql') || Schema::hasColumn($this->table, 'created_by'))
            $this->model->created_by = Auth::user()?->{$this->primaryKey} ?? null;

        // Set default uuid
        if (!str_contains($this->driver, 'sql') || Schema::hasColumn($this->table, 'uuid'))
            $this->model->uuid = $request->uuid ?? Uuid::uuid();

        try {
            $this->model->save();

            $this->postAdd($request, $this->model);
            if ($transaction)
                DB::commit();

            return $this->model;
        } catch (Exception $e) {
            if ($transaction)
                DB::rollBack();

            throw new QueryException($e->getMessage() ?? __('system-500'), $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function update(int|string $id, object $request, bool $transaction = false): Model
    {
        if ($transaction)
            DB::beginTransaction();

        // Validate
        if ($this->validateUpdateRequest($id, $request) !== true)
            return $this->model;

        $model = $this->get($id);

        $requestArr = $this->toArray($request);

        foreach ($requestArr as $column => $value) {
            if ($this->fillAbles === ['*']
                || (in_array($column, $this->fillAbles) && !in_array($column, $this->guarded))
            )
                $model->{$column} = $this->handleRequestValue($value);
        }

        try {
            $model->save();

            $this->postUpdate($id, $request, $model);
            if ($transaction)
                DB::commit();

            return $model;
        } catch (Exception $e) {
            if ($transaction)
                DB::rollBack();
            throw new SystemException($e->getMessage() ?? __('system-500'), $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function putUpdate(int|string $id, object $request, bool $transaction = false): Model
    {
        if ($transaction)
            DB::beginTransaction();

        // Validate
        if ($this->validateUpdateRequest($id, $request) !== true)
            return $this->model;

        $model    = $this->get($id);
        $modelArr = $model->toArray();

        foreach ($modelArr as $column => $value) {
            // Ignore data if it can be not modified
            if ($column === $this->primaryKey || $column === 'uuid' || $column === 'created_by')
                continue;
            if ($this->fillAbles === ['*'] || !in_array($column, $this->fillAbles))
                continue;
            if (in_array($column, $this->guarded))
                continue;

            $model->{$column} = $this->handleRequestValue($request->{$column});
        }

        try {
            $model->save();

            $this->postPutUpdate($id, $request, $model);
            if ($transaction)
                DB::commit();

            return $model;
        } catch (Exception $e) {
            if ($transaction)
                DB::rollBack();
            throw new SystemException($e->getMessage() ?? __('system-500'), $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function deleteByUuid(string $uuid, bool $transaction = false): bool
    {
        if ($transaction)
            DB::beginTransaction();
        $data = $this->getByUuid($uuid);
        try {
            $deleted = $data->delete();
            $this->postDeleteByUuid($uuid);
            if ($transaction)
                DB::commit();

            return $deleted;
        } catch (Exception $e) {
            if ($transaction)
                DB::rollBack();
            throw new SystemException(
                ['message' => __('can-not-del', ['attribute' => __('entity')]) . ": $uuid"],
                $e
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function getByUuid(string $uuid): Model
    {
        if ($this instanceof ShouldCache && Cache::has($this->model . "-$uuid"))
            return Cache::get($this->model . "-$uuid");

        $entity = $this->relate($this->model->query())->where('uuid', '=', $uuid)->firstOrFail();

        $this->postGetByUuid($uuid, $entity);

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function delete(int|string $id, bool $transaction = false): bool
    {
        if ($transaction)
            DB::beginTransaction();

        $data = $this->get($id);
        try {
            $deleted = $data->delete();
            $this->postDelete($id);
            if ($transaction)
                DB::commit();

            return $deleted;
        } catch (Exception $e) {
            if ($transaction)
                DB::rollBack();

            throw new QueryException(
                ['message' => __('can-not-del', ['attribute' => __('entity')]) . ": $id"],
                $e
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function deleteByIds(object $request, bool $transaction = false): bool
    {
        if ($transaction)
            DB::beginTransaction();
        $this->doValidate($request, ['ids' => 'required']);
        $ids = explode(',', $request->ids ?? '');

        $data = $this->model->query()->whereIn($this->primaryKey, $ids);
        try {
            $deleted = $data->delete();
            $this->postDeleteByIds($request);
            if ($transaction)
                DB::commit();

            return $deleted;
        } catch (Exception $e) {
            if ($transaction)
                DB::rollBack();

            throw new QueryException(
                ['message' => __('can-not-del', ['attribute' => __('entity')]) . ": $request->ids"],
                $e
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function deleteByUuids(object $request, bool $transaction = false): bool
    {
        if ($transaction)
            DB::beginTransaction();
        $uuids = explode(',', $request->uuids ?? '');

        $data = $this->model->query()->whereIn('uuid', $uuids);
        try {
            $deleted = $data->delete();
            $this->postDeleteByUuids($request);
            if ($transaction)
                DB::commit();

            return $deleted;
        } catch (Exception $e) {
            if ($transaction)
                DB::rollBack();

            throw new QueryException(
                ['message' => __('can-not-del', ['attribute' => __('entity')]) . ": $request->uuids"],
                $e
            );
        }
    }
}
