<?php

namespace YaangVu\LaravelBase\Services;


use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

interface BaseServiceInterface
{
    /**
     * Create new Model
     * @return void
     */
    function createModel(): void;

    /**
     * Get list of all items
     */
    public function getAll(): LengthAwarePaginator;

    /**
     * Get Entity via Id
     *
     * @param int|string $id
     */
    public function get(int|string $id): Model;

    /**
     * Delete a Entity via ID
     *
     * @param int|string $id
     */
    public function delete(int|string $id): bool;

    /**
     * Store new Entity
     *
     * @param object $request
     */
    public function add(object $request): Model;

    /**
     * Update an Entity via ID
     *
     * @param int|string $id
     * @param object     $request
     */
    public function update(int|string $id, object $request): Model;

    /**
     * Validate store request
     *
     * @param object $request
     */
    public function storeRequestValidate(object $request);

    /**
     * @param int|string $id
     * @param object     $request
     */
    public function updateRequestValidate(int|string $id, object $request);

    /**
     * Set relation
     *
     * @param array|string $relations
     */
    public function with(array|string $relations);

    /**
     * Get Current User logged in
     */
    public static function currentUser();

    /**
     * @param object $user
     */
    public static function setCurrentUser(object $user): void;

    /**
     * @param object $request
     *
     * @return mixed
     */
    public function preAdd(object $request): mixed;

    /**
     * @param object $request
     * @param Model  $model
     *
     * @return mixed
     */
    public function postAdd(object $request, Model $model): mixed;

    /**
     * @param int|string $id
     * @param object     $request
     *
     * @return mixed
     */
    public function preUpdate(int|string $id, object $request): mixed;

    /**
     * @param int|string $id
     * @param object     $request
     * @param Model      $model
     *
     * @return mixed
     */
    public function postUpdate(int|string $id, object $request, Model $model): mixed;

    /**
     * @param int|string $id
     *
     * @return mixed
     */
    public function preGet(int|string $id): mixed;

    /**
     * @param int|string $id
     * @param Model      $model
     *
     * @return mixed
     */
    public function postGet(int|string $id, Model $model): mixed;

    /**
     * @return mixed
     */
    public function preGetAll(): mixed;

    /**
     * @param Model $model
     *
     * @return mixed
     */
    public function postGetAll(Model $model): mixed;

    /**
     * @param int|string $id
     *
     * @return mixed
     */
    public function preDelete(int|string $id): mixed;

    /**
     * @param int|string $id
     *
     * @return mixed
     */
    public function postDelete(int|string $id): mixed;

}
