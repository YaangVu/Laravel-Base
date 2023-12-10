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

    public function __construct(string                  $message = "", int $code = 0, ?Throwable $previous = null,
                                private readonly string $error = '', private readonly array $messages = [])
    {
        parent::__construct($message, $code, $previous);
    }

    public function render($request): JsonResponse
    {
        $response = [
            'message'  => $this->getMessage(),
            'messages' => $this->getMessages(),
            'error'    => $this->getError(),
            'code'     => $this->getCode(),
            'file'     => $this->getFile(),
            'line'     => $this->getLine(),
            'trace'    => $this->getTrace(),
        ];

        Log::error($this->getMessage(), $response);

        // if the application was not enabling debug mode, then return only a message
        if (!config('app.debug'))
            $response = [
                'message'  => $this->getMessage(),
                'messages' => $this->getMessages(),
            ];

        return response()->json($response)->setStatusCode($this->statusCode);
    }

    public function getMessages(): array
    {
        return $this->messages;
    }

    public function getError(): string
    {
        return $this->error;
    }
}
