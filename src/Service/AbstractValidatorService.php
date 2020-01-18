<?php
/**
 * Copyright (c) 2020.
 *
 * Class AbstractValidatorService.php
 *
 * @author      Fabian Fröhlich <mail@f-froehlich.de>
 *
 * @package     core-api
 * @since       Sat, Jan 18, '20
 */

namespace FabianFroehlich\Core\Api\Service;


use FabianFroehlich\Core\Api\Connection\ApiRequest;
use FabianFroehlich\Core\Api\Connection\JsonApiResponse;

abstract class AbstractValidatorService {


    /**
     * Validiert die Response
     *
     *
     *
     * @param JsonApiResponse $response
     *
     * @return bool
     */
    abstract function validateResponse(JsonApiResponse $response): bool;

    /**
     * Validiert den Request
     *
     * @param ApiRequest $request
     *
     * @return bool
     */
    abstract function validateRequest(ApiRequest $request): bool;

}