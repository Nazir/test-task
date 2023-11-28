<?php

namespace App\Exception;

use App\Model\Api\ApiResponse;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Throwable;

use function is_array;

class ValidationException extends BaseException
{
    public function __construct(
        string|array $message = '',
        int $code = ExceptionDef::EXCEPTION_CODE_DEFAULT,
        Throwable $previous = null,
    ) {
        $details = [];
        if (is_array($message)) {
            $details = $message;
            $message = 'Bad request';
        }

        parent::__construct(
            statusCode: ApiResponse::HTTP_BAD_REQUEST,
            details: $details,
            message: $message,
            previous: $previous,
            code: $code,
        );
    }

    /**
     * Формирует сообщение об ошибке валидации.
     *
     * @param string $fieldName
     * @param string $message
     * @param mixed  $value
     *
     * @return self
     */
    public static function createValidationException(
        string $fieldName,
        string $message,
        mixed $value = null,
    ): self {
        return new self(
            [
                'propertyPath' => $fieldName,
                'message' => $message,
                'parameters' =>
                    [
                        $fieldName => $value
                    ],
            ]
        );
    }

    public static function fromViolations(ConstraintViolationListInterface $list): self
    {
        $message = [];

        foreach ($list as $violation) {
            $message[] = [
                'propertyPath' => $violation->getPropertyPath(),
                'message' => $violation->getMessage(),
                'parameters' => $violation->getParameters(),
            ];
        }

        return new self(message: $message);
    }
}
