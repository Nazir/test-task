<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class BaseException extends HttpException
{
    private array $details = [];

    public function __construct(
        int $statusCode,
        string|null $message = '',
        array $details = [],
        Throwable $previous = null,
        array $headers = [],
        int|null $code = ExceptionDef::EXCEPTION_CODE_DEFAULT,
    ) {
        parent::__construct($statusCode, $message ?? '', $previous, $headers, $code ?? 0);

        $this->details = $details;
    }

    public function getDetails(): array
    {
        return $this->details;
    }
}
