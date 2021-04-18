<?php

namespace YaangVu\LaravelBase\Exceptions;

use Exception;
use Illuminate\Http\Response;

class ForbiddenException extends BaseException
{
    public function __construct(string|array $message, Exception $e)
    {
        parent::__construct($message, $e, Response::HTTP_FORBIDDEN);
    }
}
