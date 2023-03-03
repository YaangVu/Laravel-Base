<?php

namespace YaangVu\LaravelBase\Exception;

use Exception;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package YaangVu\LaravelBase\Exceptions
 * @OA\Schema(
 *     schema="UnauthorizedException"
 * )
 */
class UnauthorizedException extends BaseException
{
    /**
     *
     * @OA\Property(
     *   property="message",
     *   oneOf={
     *     @OA\Schema(type="string"),
     *     @OA\Schema(
     *          type="array",
     *          @OA\Items({})
     *     ),
     *   }
     * ),
     * @OA\Property(
     *     property="code",
     *     type="integer",
     *     format="int64",
     *     default="401"
     * ),
     *
     */
    public function __construct(string|array $message, ?Exception $e = null)
    {
        parent::__construct($message, $e, Response::HTTP_UNAUTHORIZED);
    }
}