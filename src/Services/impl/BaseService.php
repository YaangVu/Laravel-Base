<?php

namespace YaangVu\LaravelBase\Services\impl;

use Exception;
use Faker\Provider\Uuid;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use YaangVu\Exceptions\BadRequestException;
use YaangVu\Exceptions\NotFoundException;
use YaangVu\Exceptions\SystemException;
use YaangVu\LaravelBase\Helpers\QueryHelper;
use YaangVu\LaravelBase\Services\BaseServiceInterface;

abstract class BaseService implements BaseServiceInterface
{

    public static bool $validateThrowAble = true;

    protected QueryHelper $queryHelper;

    public Model|Builder $model;

    protected string $driver;

    public static object|null $currentUser = null;

    public function __construct()
    {
        $this->createModel();
        // Initial Query Facade
        $this->driver      = $this->model->getConnection()->getDriverName();
        $this->queryHelper = new QueryHelper($this->driver);
    }

    /**
     * Create new Model
     * @return void
     */
    abstract function createModel(): void;

    /**
     * Get list of all items
     * @return LengthAwarePaginator
     * @throws SystemException
     */
    public function getAll(): LengthAwarePaginator
    {
        $this->preGetAll();
        $data = $this->queryHelper->buildQuery($this->model);
        try {
            $response = $data->paginate(QueryHelper::limit());
            $this->postGetAll($response);

            return $response;
        } catch (Exception $e) {
            throw new SystemException($e->getMessage() ?? __('system-500'), $e);
        }
    }

    /**
     * Get Entity via Id
     *
     * @param int|string $id
     *
     * @return Model
     */
    public function get(int|string $id): Model
    {
        $this->preGet($id);
        try {
            if ($this->queryHelper->relations)
                $this->model = $this->model->with($this->queryHelper->relations);

            $entity = $this->model->findOrFail($id);
            $this->postGet($id, $entity);

            return $entity;
        } catch (ModelNotFoundException $e) {
            throw new NotFoundException(
                ['message' => __("not-exist", ['attribute' => __('entity')]) . ": $id"],
                $e
            );
        } catch (Exception $e) {
            throw new SystemException($e->getMessage() ?? __('system-500'), $e);
        }
    }

    /**
     * Get Entity via uuid
     *
     * @param string $uuid
     *
     * @return Model
     */
    public function getByUuid(string $uuid): Model
    {
        $this->preGetByUuid($uuid);
        try {
            if ($this->queryHelper->relations)
                $this->model = $this->model->with($this->queryHelper->relations);

            $entity = $this->model->where('uuid', $uuid)->firstOrFail();
            $this->postGetByUuid($uuid, $entity);

            return $entity;
        } catch (ModelNotFoundException $e) {
            throw new NotFoundException(
                ['message' => __("not-exist", ['attribute' => __('entity')]) . ": $uuid"],
                $e
            );
        } catch (Exception $e) {
            throw new SystemException($e->getMessage() ?? __('system-500'), $e);
        }
    }

    /**
     * @param string $uuid
     */
    public function preGetByUuid(string $uuid)
    {
        // TODO: Implement preGetByUuid() method.
    }

    /**
     * @param string $uuid
     * @param Model  $model
     */
    public function postGetByUuid(string $uuid, Model $model)
    {
        // TODO: Implement postGetByUuid() method.
    }

    /**
     * Delete a Entity via ID
     *
     * @param int|string $id
     *
     * @return bool
     */
    public function delete(int|string $id): bool
    {
        DB::beginTransaction();
        $this->preDelete($id);
        $data = $this->get($id);
        try {
            $deleted = $data->delete();
            $this->postDelete($id);
            DB::commit();

            return $deleted;
        } catch (Exception $e) {
            DB::rollBack();
            throw new SystemException(
                ['message' => __('can-not-del', ['attribute' => __('entity')]) . ": $id"],
                $e
            );
        }
    }

    /**
     * Delete a Entity via uuid
     *
     * @param string $uuid
     *
     * @return bool
     */
    public function deleteByUuid(string $uuid): bool
    {
        DB::beginTransaction();
        $this->preDeleteByUuid($uuid);
        $data = $this->getByUuid($uuid);
        try {
            $deleted = $data->delete();
            $this->postDeleteByUuid($uuid);
            DB::commit();

            return $deleted;
        } catch (Exception $e) {
            DB::rollBack();
            throw new SystemException(
                ['message' => __('can-not-del', ['attribute' => __('entity')]) . ": $uuid"],
                $e
            );
        }
    }

    /**
     * Store new Entity
     *
     * @param object $request
     *
     * @return Model
     */
    public function add(object $request): Model
    {
        DB::beginTransaction();
        $request = $this->preAdd($request) ?? $request;

        // Set data to new entity
        $fillAbles = $this->model->getFillable();
        $guarded   = $this->model->getGuarded();

        // Validate
        if ($this->storeRequestValidate($request) !== true)
            return $this->model;

        if ($fillAbles === ['*']) { // Insert all data to DB
            if ($request instanceof Request)
                $requestArr = $request->toArray();
            else
                $requestArr = (array)$request;

            foreach ($requestArr as $column => $value)
                if (!in_array($column, $guarded))
                    $this->model->{$column} = $this->_handleRequestData($value);
        } else // Only insert specific data
            foreach ($fillAbles as $fillAble)
                if (isset($request->$fillAble) && !in_array($fillAble, $guarded))
                    $this->model->$fillAble = $this->_handleRequestData($request->$fillAble);

        // Set created_by is current user
        $this->model->created_by = self::currentUser()?->id ?? null;

        // Set default uuid
        if (!str_contains($this->driver, 'sql') || Schema::hasColumn($this->model->getTable(), 'uuid'))
            $this->model->uuid = $request->uuid ?? Uuid::uuid();

        try {
            $this->model->save();
            $this->postAdd($request, $this->model);
            DB::commit();

            return $this->model;
        } catch (Exception $e) {
            DB::rollBack();
            throw new SystemException($e->getMessage() ?? __('system-500'), $e);
        }
    }

    /**
     * Update an Entity via ID
     *
     * @param int|string $id
     * @param object     $request
     *
     * @return Model
     */
    public function update(int|string $id, object $request): Model
    {
        DB::beginTransaction();
        $request = $this->preUpdate($id, $request) ?? $request;

        // Set data for updated entity
        $fillAbles = $this->model->getFillable();
        $guarded   = $this->model->getGuarded();

        // Validate
        if ($this->updateRequestValidate($id, $request) !== true)
            return $this->model;

        $model = $this->get($id);

        if ($fillAbles === ['*']) { // Insert all data to DB
            if ($request instanceof Request)
                $requestArr = $request->toArray();
            else
                $requestArr = (array)$request;

            foreach ($requestArr as $column => $value)
                if (!in_array($column, $guarded))
                    $model->{$column} = $this->_handleRequestData($value) ?? $model->{$column};
        } else
            foreach ($fillAbles as $fillAble)
                if (isset($request->$fillAble) && !in_array($fillAble, $guarded))
                    $model->$fillAble = $this->_handleRequestData($request->$fillAble) ?? $model->$fillAble;

        if (isset($model->uuid) && $model->uuid === null)
            $model->uuid = Uuid::uuid();

        try {
            $model->save();
            $this->postUpdate($id, $request, $model);
            DB::commit();

            return $model;
        } catch (Exception $e) {
            DB::rollBack();
            throw new SystemException($e->getMessage() ?? __('system-500'), $e);
        }
    }

    /**
     * Delete multiple Entity via IDs
     *
     * @param object $request
     *
     * @return bool
     */
    public function deleteByIds(object $request): bool
    {
        $this->preDeleteByIds($request);
        $this->doValidate($request, ['ids' => 'required|array']);
        $idField = $this->model->getKey();
        if (!Schema::hasColumn($this->model->getTable(), $idField))
            throw new BadRequestException(__("not-exist", ['attribute' => __('entity')]));

        $data = $this->model->whereIn($idField, $request->ids);
        try {
            $deleted = $data->delete();
            $this->postDeleteByIds($request);

            return $deleted;
        } catch (Exception $e) {
            throw new SystemException($e->getMessage() ?? __('system-500'), $e);
        }
    }

    /**
     * Delete multiple Entity via Uuids
     *
     * @param object $request
     *
     * @return bool
     */
    public function deleteByUuids(object $request): bool
    {
        $this->preDeleteByUuids($request);
        $this->doValidate($request, ['uuids' => 'required|array']);

        $data = $this->model->whereIn('uuid', $request->uuids);
        try {
            $deleted = $data->delete();
            $this->postDeleteByUuids($request);

            return $deleted;
        } catch (Exception $e) {
            throw new SystemException($e->getMessage() ?? __('system-500'), $e);
        }
    }

    /**
     * Validate store request
     *
     * @param object $request
     * @param array  $rules
     * @param array  $messages
     *
     * @return bool|array
     */
    public function storeRequestValidate(object $request, array $rules = [], array $messages = []): bool|array
    {
        if (!$rules)
            return true;

        return $this->doValidate($request, $rules, $messages);
    }

    /**
     * Validate update request
     *
     * @param int|string $id
     * @param object     $request
     * @param array      $rules
     * @param array      $messages
     *
     * @return bool|array
     */
    public function updateRequestValidate(int|string $id, object $request, array $rules = [],
                                          array      $messages = []): bool|array
    {
        if (!$rules || !$id)
            return true;

        return $this->doValidate($request, $rules, $messages);
    }

    /**
     * @param object $request
     * @param array  $rules
     * @param array  $messages
     *
     * @return bool|array
     */
    public static function doValidate(object $request, array $rules = [], array $messages = []): bool|array
    {
        if ($request instanceof Request)
            $request = $request->all();
        elseif ($request instanceof Model)
            $request = $request->toArray();
        else
            $request = (array)$request;

        $validator = Validator::make($request, $rules, $messages);

        if ($validator?->fails()) {
            if (self::$validateThrowAble)
                throw new BadRequestException(['messages' => $validator->errors()->toArray()], new Exception());
            else
                return $validator->errors()->toArray();
        }

        return true;
    }

    /**
     * Set relation
     *
     * @param array|string $relations
     *
     * @return BaseService
     */
    public function with(array|string $relations): static
    {
        $this->queryHelper->with($relations);

        return $this;
    }

    /**
     * Get Current User logged in
     */
    public static function currentUser(): object|null
    {
        return self::$currentUser ?? Auth::user();
    }

    /**
     * @param object $user
     *
     * @return void
     */
    public static function setCurrentUser(object $user): void
    {
        self::$currentUser = $user;
    }

    /**
     * @param object $request
     */
    public function preAdd(object $request)
    {
        // TODO: Implement preAdd() method.
    }

    /**
     * @param object $request
     * @param Model  $model
     */
    public function postAdd(object $request, Model $model)
    {
        // TODO: Implement postAdd() method.
    }

    /**
     * @param int|string $id
     */
    public function preGet(int|string $id)
    {
        // TODO: Implement preGet() method.
    }

    /**
     * @param int|string $id
     * @param Model      $model
     */
    public function postGet(int|string $id, Model $model)
    {
        // TODO: Implement postGet() method.
    }

    /**
     */
    public function preGetAll()
    {
        // TODO: Implement preGetAll() method.
    }

    /**
     * @param object $model
     */
    public function postGetAll(object $model)
    {
        // TODO: Implement postGetAll() method.
    }

    /**
     * @param int|string $id
     * @param object     $request
     */
    public function preUpdate(int|string $id, object $request)
    {
        // TODO: Implement preUpdate() method.
    }

    /**
     * @param int|string $id
     * @param object     $request
     * @param Model      $model
     */
    public function postUpdate(int|string $id, object $request, Model $model)
    {
        // TODO: Implement postUpdate() method.
    }

    /**
     * @param int|string $id
     */
    public function preDelete(int|string $id)
    {
        // TODO: Implement preDelete() method.
    }

    /**
     * @param int|string $id
     */
    public function postDelete(int|string $id)
    {
        // TODO: Implement postDelete() method.
    }

    /**
     * @param string $uuid
     */
    public function preDeleteByUuid(string $uuid)
    {
        // TODO: Implement preDelete() method.
    }

    /**
     * @param string $uuid
     */
    public function postDeleteByUuid(string $uuid)
    {
        // TODO: Implement postDelete() method.
    }

    /**
     * @param object $request
     */
    public function preDeleteByIds(object $request)
    {
        // TODO: Implement preDelete() method.
    }

    /**
     * @param object $request
     */
    public function postDeleteByIds(object $request)
    {
        // TODO: Implement postDelete() method.
    }

    /**
     * @param object $request
     */
    public function preDeleteByUuids(object $request)
    {
        // TODO: Implement preDelete() method.
    }

    /**
     * @param object $request
     */
    public function postDeleteByUuids(object $request)
    {
        // TODO: Implement postDelete() method.
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    private function _handleRequestData(mixed $value): mixed
    {
        if (gettype($value) === 'string')
            return trim($value);
        else
            return $value;
    }

}
