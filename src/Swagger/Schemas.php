<?php
/**
 * @Author phuongnam
 * @Date   Feb 09, 2022
 */

namespace YaangVu\LaravelBase\Swagger;

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
class Schemas
{

}