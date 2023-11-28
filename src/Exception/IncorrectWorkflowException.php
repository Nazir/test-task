<?php

namespace App\Exception;

use LogicException;
use Throwable;

class IncorrectWorkflowException extends LogicException
{
    public function __construct(
        string|null $message = null,
        int $code = ExceptionDef::EXCEPTION_CODE_DEFAULT,
        Throwable $previous = null,
    ) {
        parent::__construct($message ?? 'Incorrect workflow', $code, $previous);
    }
}
