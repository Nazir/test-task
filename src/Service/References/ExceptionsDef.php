<?php

declare(strict_types=1);

namespace App\Service\References;

final class ExceptionsDef
{
    /**
     * @var int EXCEPTION_CODE_REF_READ Reference read exception code
     */
    public const EXCEPTION_CODE_REF_READ = 100;

    /**
     * @var int EXCEPTION_CODE_REF_CREATE Reference create exception code
     */
    public const EXCEPTION_CODE_REF_CREATE = 101;

    /**
     * @var int EXCEPTION_CODE_REF_UPDATE Reference update exception code
     */
    public const EXCEPTION_CODE_REF_UPDATE = 102;
}
