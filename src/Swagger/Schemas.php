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
 *     @OA\Property(
 *          property="message",
 *          oneOf={
 *              @OA\Schema(type="string"),
 *              @OA\Schema(
 *                  type="array",
 *                  @OA\Items({})
 *              ),
 *          }
 *      ),
 *      @OA\Property(property="error", type="string"),
 *      @OA\Property(property="code", type="number", format="int64"),
 *      @OA\Property(property="file", type="string"),
 *      @OA\Property(property="line", type="number", format="int64"),
 *      @OA\Property(property="trace", type="string"),
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
class Schemas
{

}