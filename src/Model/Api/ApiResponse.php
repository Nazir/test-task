<?php

namespace App\Model\Api;

use App\Common\CommonDef;
use App\Serializer\SerializerDef;
use App\Service\Common\SerializerService;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * API Response
 */
final class ApiResponse extends JsonResponse
{
    public function __construct(
        mixed $data = null,
        int $status = self::HTTP_OK,
        array $headers = [],
        bool $json = false,
        string $datetimeFormat = CommonDef::API_DATE_TIME_FORMAT,
        null|array $context = null,
        null|array|string $groups = SerializerDef::DEFAULT_GROUPS,
    ) {
        $this->charset = 'UTF-8';

        $data = isset($data) ? SerializerService::normalize(
            data: $data,
            context: $context,
            datetimeFormat: $datetimeFormat,
            groups: $groups,
        ) : $data;

        parent::__construct(data: $data, status: $status, headers: $headers, json: $json);
    }
}
