<?php

namespace YaangVu\LaravelBase\Services\impl;

use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use YaangVu\Exceptions\BadRequestException;
use YaangVu\Exceptions\NotFoundException;
use YaangVu\Exceptions\SystemException;
use YaangVu\LaravelBase\Helpers\QueryHelper;
use YaangVu\LaravelBase\Services\BaseServiceInterface;

abstract class BaseService implements BaseServiceInterface
{

    public bool $validateThrowAble = true;

    protected QueryHelper $queryHelper;

    public Model|Builder $model;

    public static object|null $currentUser = null;

    public function __construct()
    {
        $this->queryHelper = new QueryHelper();
        $this->createModel();
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
     * Get Entity via Code
     *
     * @param string $code
     *
     * @return Model
     */

    public function getByCode(string $code): Model
    {
        $this->preGetByCode($code);
        try {
            if ($this->queryHelper->relations)
                $this->model = $this->model->with($this->queryHelper->relations);

            $codeField = $this->model->code;
            if (!Schema::hasColumn($this->model->getTable(), $codeField))
                throw new BadRequestException(__("not-exist", ['attribute' => __('entity')]));

            $entity = $this->model->where($codeField, $code)->firstOrFail();
            $this->postGetByCode($code, $entity);

            return $entity;
        } catch (ModelNotFoundException $e) {
            throw new NotFoundException(
                ['message' => __("not-exist", ['attribute' => __('entity')]) . ": $code"],
                $e
            );
        } catch (Exception $e) {
            throw new SystemException($e->getMessage() ?? __('system-500'), $e);
        }
    }

    /**
     * @param string $code
     */
    public function preGetByCode(string $code)
    {
        // TODO: Implement preGetByCode() method.
    }

    /**
     * @param string $code
     * @param Model  $model
     */
    public function postGetByCode(string $code, Model $model)
    {
        // TODO: Implement postGetByCode() method.
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
        $this->preDelete($id);
        $data = $this->get($id);
        try {
            $deleted = $data->delete();
            $this->postDelete($id);

            return $deleted;
        } catch (Exception $e) {
            throw new SystemException(
                ['message' => __('can-not-del', ['attribute' => __('entity')]) . ": $id"],
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
        $request = $this->preAdd($request) ?? $request;

        // Validate
        if ($this->storeRequestValidate($request) !== true)
            return $this->model;

        // Set data to new entity
        $fillAbles = $this->model->getFillable();
        foreach ($fillAbles as $fillAble)
            if (isset($request->$fillAble))
                $this->model->$fillAble = $request->$fillAble;

        // Set created_by is current user
        if (Schema::hasColumn($this->model->getTable(), 'created_by'))
            $this->model->created_by = self::currentUser()?->id ?? null;

        try {
            $this->model->save();
            $this->postAdd($request, $this->model);

            return $this->model;
        } catch (Exception $e) {
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
        $request = $this->preUpdate($id, $request) ?? $request;

        // Validate
        if ($this->updateRequestValidate($id, $request) !== true)
            return $this->model;

        $model = $this->get($id);

        // Set data for updated entity
        $fillAbles = $model->getFillable();
        foreach ($fillAbles as $fillAble)
            if (isset($request->$fillAble))
                $model->$fillAble = $request->$fillAble;
        try {
            $model->save();
            $this->postUpdate($id, $request, $model);

            return $model;
        } catch (Exception $e) {
            throw new SystemException($e->getMessage() ?? __('system-500'), $e);
        }
    }

    /**
     * Validate store request
     *
     * @param object $request
     * @param array  $rules
     *
     * @return bool|array
     */
    public function storeRequestValidate(object $request, array $rules = []): bool|array
    {
        if (!$rules || !$request)
            return true;

        return $this->_doValidate($request, $rules);
    }

    /**
     * Validate update request
     *
     * @param int|string $id
     * @param object     $request
     * @param array      $rules
     *
     * @return bool|array
     */
    public function updateRequestValidate(int|string $id, object $request, array $rules = []): bool|array
    {
        if (!$rules || !$id || !$request)
            return true;

        return $this->_doValidate($request, $rules);
    }

    /**
     * @param object $request
     * @param array  $rules
     *
     * @return bool|array
     */
    private function _doValidate(object $request, array $rules = []): bool|array
    {
        if ($request instanceof Request)
            $request = $request->all();
        elseif ($request instanceof Model)
            $request = $request->toArray();
        else
            $request = (array)$request;

        $validator = Validator::make($request, $rules);

        if ($validator?->fails()) {
            if ($this->validateThrowAble)
                throw new BadRequestException($validator->errors()->toArray(), new Exception());
            else
                return $validator->errors()->toArray();
        }

        return true;
    }

    /**
     * Set relation
     *
     * @param array|string $relations
     */
    public function with(array|string $relations)
    {
        $this->queryHelper->with($relations);
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
}
