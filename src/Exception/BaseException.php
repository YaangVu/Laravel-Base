<?php

namespace YaangVu\LaravelBase\Exception;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class BaseException extends Exception
{
    public int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;

    public function __construct(string     $message = "", int $code = 0,
                                ?Throwable $previous = null, private readonly string $error = '')
    {
        parent::__construct($message, $code, $previous);
    }

    public function render($request): JsonResponse
    {
        $response = [
            'message' => $this->getMessage(),
            'error'   => $this->getError(),
            'code'    => $this->getCode(),
            'file'    => $this->getFile(),
            'line'    => $this->getLine(),
            'trace'   => $this->getTrace(),
        ];

        Log::error($this->getMessage(), $response);

        return response()->json($response)->setStatusCode($this->statusCode);
    }

    public function getError(): string
    {
        return $this->error;
    }
}
