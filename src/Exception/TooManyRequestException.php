<?php

namespace YaangVu\LaravelBase\Exception;

use Exception;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package YaangVu\LaravelBase\Exceptions
 * @OA\Schema(
 *     schema="TooManyRequestException"
 * )
 */
class TooManyRequestException extends BaseException
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
     *     default="429"
     * ),
     *
     */
    public function __construct(string|array $message, ?Exception $e = null)
    {
        parent::__construct($message, $e, Response::HTTP_TOO_MANY_REQUESTS);
    }
}
