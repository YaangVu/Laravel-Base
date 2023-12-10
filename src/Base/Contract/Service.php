<?php
/**
 * @Author yaangvu
 * @Date   Aug 06, 2022
 */

namespace YaangVu\LaravelBase\Base\Contract;

use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use YaangVu\LaravelBase\Exception\NotFoundException;
use YaangVu\LaravelBase\Exception\QueryException;
use YaangVu\LaravelBase\Exception\BadRequestException;

interface Service
{
    /**
     * Get a list of all items
     *
     * @param bool $paginated
     *
     * @return LengthAwarePaginator|Collection
     * @throws QueryException
     */
    public function get(bool $paginated = true): LengthAwarePaginator|Collection;

    /**
     * Get Entity via id
     *
     * @param int|string $id
     *
     * @return Model
     * @throws QueryException
     * @throws NotFoundException
     */
    public function find(int|string $id): Model;

    /**
     * Get Entity via uuid
     *
     * @param string $uuid
     *
     * @return Model
     * @throws QueryException
     */
    public function findByUuid(string $uuid): Model;

    /**
     * Store new Entity
     *
     * @param object $request
     * @param bool   $transaction
     *
     * @return Model
     * @throws QueryException
     * @throws BadRequestException
     */
    public function add(object $request, bool $transaction = false): Model;

    /**
     * Update an Entity via ID
     *
     * @param int|string $id
     * @param object     $request
     * @param bool       $transaction
     *
     * @return Model
     * @throws NotFoundException
     * @throws QueryException
     * @throws BadRequestException
     */
    public function update(int|string $id, object $request, bool $transaction = false): Model;

    /**
     * Put Update an Entity via ID
     *
     * @param int|string $id
     * @param object     $request
     * @param bool       $transaction
     *
     * @return Model
     * @throws NotFoundException
     * @throws QueryException
     * @throws BadRequestException
     */
    public function putUpdate(int|string $id, object $request, bool $transaction = false): Model;

    /**
     * Delete an Entity via ID
     *
     * @param int|string $id
     * @param bool       $transaction
     *
     * @return bool
     * @throws NotFoundException
     * @throws QueryException
     */
    public function delete(int|string $id, bool $transaction = false): bool;

    /**
     * Delete a Entity via uuid
     *
     * @param string $uuid
     * @param bool   $transaction
     *
     * @return bool
     * @throws QueryException
     */
    public function deleteByUuid(string $uuid, bool $transaction = false): bool;

    /**
     * Delete multiple Entity via IDs
     *
     * @param object $request
     * @param bool   $transaction
     *
     * @return bool
     * @throws QueryException
     */
    public function deleteByIds(object $request, bool $transaction = false): bool;

    /**
     * Delete multiple Entity via Uuids
     *
     * @param object $request
     * @param bool   $transaction
     *
     * @return bool
     * @throws QueryException
     */
    public function deleteByUuids(object $request, bool $transaction = false): bool;
}
