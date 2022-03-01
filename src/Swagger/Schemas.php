<?php
/**
 * @Author phuongnam
 * @Date   Feb 09, 2022
 */

/**
 * @package App\Exceptions
 * @OA\Schema(
 *     schema="Exceptions",
 *     type="object",
 *     description="",
 *     @OA\Property(property="messages", type="object"),
 *     @OA\Property(property="message", type="string")
 * )
 */

/**
 *
 * @Author         YaangVu
 * @Date           Jan 19, 2022
 * @Description    Component Pagination.
 *
 * @OA\Schema(
 *      schema="Pagination",
 *      title="Pagination Result",
 *      type="object",
 *      description="",
 *      @OA\Property(property="total", type="number", format="int64", default=1),
 *      @OA\Property(property="page", type="number", format="int64", default=1),
 *      @OA\Property(property="per_page", type="number", format="int64", default=10),
 *      @OA\Property(property="current_page", type="number", format="int64", default=1),
 *      @OA\Property(property="last_page", type="number", format="int64"),
 *      @OA\Property(property="from", type="number", format="int64", default=1),
 *      @OA\Property(property="to", type="number", format="int64", default=1),
 *      @OA\Property(property="first_page_url", type="string"),
 *      @OA\Property(property="last_page_url", type="string"),
 *      @OA\Property(property="next_page_url", type="string", nullable=true),
 *      @OA\Property(property="prev_page_url", type="string", nullable=true),
 *      @OA\Property(property="path", type="string"),
 *      @OA\Property(
 *          property="data",
 *          type="array",
 *          @OA\Items()
 *      )
 * )
 *
 */

/**
 * @package         domains\Base\Parameter
 * @Author          TungND
 * @Description     Component Parameters.
 *
 * @OA\Parameter(
 *     parameter="asset--limit",
 *     name="limit",
 *     description="Limit",
 *     required=false,
 *     in="query",
 *     @OA\Schema(
 *        type="string",
 *        default="10"
 *     ),
 * ),
 * @OA\Parameter(
 *     parameter="general--page",
 *     name="page",
 *     description="Page",
 *     required=false,
 *     in="query",
 *      @OA\Schema(
 *        type="string",
 *        default="1"
 *      ),
 * ),
 * @OA\Parameter(
 *     parameter="asset--order_by",
 *     name="order_by",
 *     description="Order by",
 *     required=false,
 *     in="query",
 *     @OA\Schema(
 *        type="string",
 *        default="id ASC"
 *     ),
 * ),
 * @OA\Parameter(
 *     parameter="asset--sid",
 *     name="id",
 *     description="Applications id",
 *     required=true,
 *     in="path",
 *      @OA\Schema(
 *        type="integer"
 *      ),
 * ),
 * @OA\Parameter(
 *     parameter="asset--iid",
 *     name="id",
 *     description="Applications id",
 *     required=true,
 *     in="path",
 *     @OA\Schema(
 *       type="integer"
 *     )
 * ),
 * @OA\Parameter(
 *     parameter="asset--uuid",
 *     name="uuid",
 *     description="Applications uuid",
 *     required=true,
 *     in="path",
 *     @OA\Schema(
 *       type="string"
 *     )
 * ),
 */

/**
 * @package         domains\Base\Property
 * @Author          TungND
 * @Description     Component Property ID.
 *
 * @OA\Schema(
 *   schema="propertyID",
 *   description="Property ID",
 *   title="Property ID Schema",
 *   required={
 *     "id"
 *   },
 *    @OA\Property(
 *      property="ids",
 *      type="array",
 *      description="Template id",
 *      @OA\Items(),
 *    )
 * )
 *
 */

/**
 * @package         domains\Base\PropertyUuid
 * @Author          TungND
 * @Description     Component Property Uuid.
 *
 * @OA\Schema(
 *   schema="propertyUuid",
 *   description="Property Uuid",
 *   title="Property Uuid Schema",
 *   required={
 *     "uuids"
 *   },
 *    @OA\Property(
 *      property="uuids",
 *      type="array",
 *      description="Template uuids",
 *      @OA\Items(),
 *    )
 * )
 *
 */

class Schemas
{

}