<?php
/**
 * Copyright (c) 2020.
 *
 * Class AbstractApiController.php
 *
 * @author      Fabian Fröhlich <mail@f-froehlich.de>
 *
 * @package     core-api
 * @since       Sun, Jan 5, '20
 */

declare(strict_types=1);
/**
 * @author    Fabian Fröhlich <mail@f-froehlich.de>
 * @date      24.12.2018
 */

namespace FabianFroehlich\Core\Api\Controller;


use FabianFroehlich\Core\Api\Connection\ApiRequest;
use FabianFroehlich\Core\Api\Connection\ApiResponse;

/**
 * Class AbstractApiController
 *
 * @package FabianFroehlich\Core\Api\Controller
 */
abstract class AbstractApiController {


    /** @var ApiResponse */
    protected $response;

    /** @var ApiRequest */
    protected $request;


    /**
     * @return ApiResponse
     */
    public function getResponse(): ApiResponse {

        return $this->response;
    }

    /**
     * @param ApiResponse $response
     */
    public function setResponse(ApiResponse $response): void {

        $this->response = $response;
    }

    /**
     * @return ApiRequest
     */
    public function getRequest(): ApiRequest {

        return $this->request;
    }

    /**
     * @param ApiRequest $request
     */
    public function setRequest(ApiRequest $request): void {

        $this->request = $request;
    }

    /**
     * @return ApiResponse
     */
    public function sendOptions(): ApiResponse {

        return $this->response;
    }

    /**
     * Gibt den DI Präfix des Validator zurück
     *
     * @return string
     */
    abstract public function getValidatorPrefix(): string;

}