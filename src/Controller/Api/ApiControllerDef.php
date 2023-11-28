<?php

namespace App\Controller\Api;

use Symfony\Component\HttpFoundation\Request;

/**
 * API Controller definition
 */
final class ApiControllerDef
{
    /**
     * Routing
     */
    public const ROUTE_PATH_ID = '/{id}';
    public const ROUTE_REQUIREMENTS_ID = ['id' => '\d+'];

    public const ROUTE_PATH_INDEX = '';
    public const ROUTE_NAME_INDEX = '_index';
    public const ROUTE_METHODS_INDEX = [Request::METHOD_GET];
    public const ROUTE_PATH_LIST = '/list';
    public const ROUTE_NAME_LIST = '_list';
    public const ROUTE_METHODS_LIST = [Request::METHOD_GET];

    /**
     * Routes for CRUD
     */
    public const ROUTE_PATH_CREATE = '/create';
    public const ROUTE_NAME_CREATE = '_create';
    public const ROUTE_METHODS_CREATE = [Request::METHOD_POST];
    public const ROUTE_PATH_READ = self::ROUTE_PATH_ID;
    public const ROUTE_NAME_READ = '_read';
    public const ROUTE_METHODS_READ = [Request::METHOD_GET];
    public const ROUTE_PATH_UPDATE = self::ROUTE_PATH_ID;
    public const ROUTE_NAME_UPDATE = '_update';
    public const ROUTE_METHODS_UPDATE = [Request::METHOD_PUT];
    public const ROUTE_PATH_PATCH = self::ROUTE_PATH_ID;
    public const ROUTE_NAME_PATCH = '_patch';
    public const ROUTE_METHODS_PATCH = [Request::METHOD_PATCH];
    public const ROUTE_PATH_DELETE = self::ROUTE_PATH_ID;
    public const ROUTE_NAME_DELETE = '_delete';
    public const ROUTE_METHODS_DELETE = [Request::METHOD_DELETE];

    /**
     * Other routes
     */
    public const ROUTE_PATH_CURRENT = '/current';
    public const ROUTE_NAME_CURRENT = '_current';
    public const ROUTE_PATH_EXISTS = '/exists';
    public const ROUTE_NAME_EXISTS = '_exists';
}
