<?php

namespace YaangVu\LaravelBase\Exceptions;

use Exception;
use Illuminate\Http\Response;

class BadRequestException extends BaseException
{
    public function __construct(array|string $message, Exception $e = null)
    {
        parent::__construct($message, $e, Response::HTTP_BAD_REQUEST);
    }
}
