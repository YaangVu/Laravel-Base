<?php

namespace YaangVu\LaravelBase\Services\impl;

use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use YaangVu\LaravelBase\Exceptions\NotFoundException;
use YaangVu\LaravelBase\Exceptions\SystemException;
use YaangVu\LaravelBase\Helpers\QueryHelper;
use YaangVu\LaravelBase\Services\BaseServiceInterface;

abstract class BaseService implements BaseServiceInterface
{

    public bool $validateThrowAble = true;

    protected QueryHelper $queryHelper;

    public Model $model;

    public static $currentUser;

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
        $data = $this->queryHelper->buildQuery($this->model);
        try {
            return $data->paginate(QueryHelper::limit());
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
        try {
            return $this->model->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundException(
                ['message' => __('not-exist' . ": $id", ['attribute' => __('entity')])],
                $e
            );
        } catch (Exception $e) {
            throw new SystemException($e->getMessage() ?? __('system-500'), $e);
        }
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
        $data = $this->get($id);
        try {
            return $data->delete();
        } catch (Exception $e) {
            throw new SystemException(
                ['message' => __('can-not-del' . ": $id", ['attribute' => __('entity')])],
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
        if ($this->storeRequestValidate($request) !== true)
            return $this->model;

        $fillAbles = $this->model->getFillable();
        foreach ($fillAbles as $fillAble)
            $this->model->$fillAble = $request->$fillAble ?? null;
        try {
            $this->model->save();

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
        if ($this->updateRequestValidate($id, $request) !== true)
            return $this->model;

        $model = $this->get($id);

        $fillAbles = $model->getFillable();
        foreach ($fillAbles as $fillAble)
            $model->$fillAble = $request->$fillAble ?? $model->$fillAble;
        try {
            $model->save();

            return $model;
        } catch (Exception $e) {
            throw new SystemException($e->getMessage() ?? __('system-500'), $e);
        }
    }

    /**
     * Validate store request
     *
     * @param object $request
     *
     * @return bool
     */
    public function storeRequestValidate(object $request): bool
    {
        // Do validate store request
        return true;
    }

    /**
     * Validate update request
     *
     * @param int|string $id
     * @param object     $request
     *
     * @return bool
     */
    public function updateRequestValidate(int|string $id, object $request): bool
    {
        // Do validate update request
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
    public static function currentUser()
    {
        return self::$currentUser;
    }

    /**
     * @param  $user
     *
     * @return void
     */
    public static function setCurrentUser($user): void
    {
        self::$currentUser = $user;
    }
}
