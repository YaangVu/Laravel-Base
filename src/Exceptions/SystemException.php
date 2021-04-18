<?php

namespace YaangVu\LaravelBase\Exceptions;

use Exception;
use Illuminate\Http\Response;

class SystemException extends BaseException
{
    public function __construct(string|array $message, Exception $e = null)
    {
        parent::__construct($message, $e, Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
