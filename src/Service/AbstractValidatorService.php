<?php
/**
 * Copyright (c) 2020.
 *
 * Class AbstractValidatorService.php
 *
 * @author      Fabian Fröhlich <mail@f-froehlich.de>
 *
 * @package     core-api
 * @since       Sun, Jan 5, '20
 */

namespace FabianFroehlich\Core\Api\Service;


use FabianFroehlich\Core\Api\Connection\ApiRequest;
use FabianFroehlich\Core\Api\Connection\ApiResponse;

abstract class AbstractValidatorService {


    /**
     * Validiert die Response
     *
     *
     *
     * @param ApiResponse $response
     *
     * @return bool
     */
    abstract function validateResponse(ApiResponse $response): bool;

    /**
     * Validiert den Request
     *
     * @param ApiRequest $request
     *
     * @return bool
     */
    abstract function validateRequest(ApiRequest $request): bool;

}