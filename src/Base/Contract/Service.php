<?php
/**
 * @Author yaangvu
 * @Date   Aug 06, 2022
 */

namespace YaangVu\LaravelBase\Base\Contract;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface Service
{
    /**
     * Get list of all items
     *
     * @param bool $paginated
     *
     * @return LengthAwarePaginator|Collection
     */
    public function get(bool $paginated = true): LengthAwarePaginator|Collection;

    /**
     * Get Entity via id
     *
     * @param int|string $id
     *
     * @return Model
     */
    public function find(int|string $id): Model;

    /**
     * Get Entity via uuid
     *
     * @param string $uuid
     *
     * @return Model
     */
    public function findByUuid(string $uuid): Model;

    /**
     * Store new Entity
     *
     * @param object $request
     * @param bool   $transaction
     *
     * @return Model
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
     */
    public function putUpdate(int|string $id, object $request, bool $transaction = false): Model;

    /**
     * Delete an Entity via ID
     *
     * @param int|string $id
     * @param bool       $transaction
     *
     * @return bool
     */
    public function delete(int|string $id, bool $transaction = false): bool;

    /**
     * Delete a Entity via uuid
     *
     * @param string $uuid
     * @param bool   $transaction
     *
     * @return bool
     */
    public function deleteByUuid(string $uuid, bool $transaction = false): bool;

    /**
     * Delete multiple Entity via IDs
     *
     * @param object $request
     * @param bool   $transaction
     *
     * @return bool
     */
    public function deleteByIds(object $request, bool $transaction = false): bool;

    /**
     * Delete multiple Entity via Uuids
     *
     * @param object $request
     * @param bool   $transaction
     *
     * @return bool
     */
    public function deleteByUuids(object $request, bool $transaction = false): bool;
}
