<?php

namespace App\Model;

use App\Exception\ExceptionDef;
use JsonSerializable;

class Error implements JsonSerializable
{
    public function __construct(
        /** HTTP response status code */
        protected int $status = Api\ApiResponse::HTTP_INTERNAL_SERVER_ERROR,
        /** Application exception code */
        protected int $code = ExceptionDef::EXCEPTION_CODE_DEFAULT,
        protected string $message = 'Error',
        protected array $details = [],
    ) {
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setCode(int $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setDetails(array $details): self
    {
        $this->details = $details;

        return $this;
    }

    public function getDetails(): array
    {
        return $this->details;
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}
