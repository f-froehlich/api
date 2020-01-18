<?php
/**
 * Copyright (c) 2020.
 *
 * Class AbstractApiController.php
 *
 * @author      Fabian Fröhlich <mail@f-froehlich.de>
 *
 * @package     core-api
 * @since       Sat, Jan 18, '20
 */

declare(strict_types=1);
/**
 * @author    Fabian Fröhlich <mail@f-froehlich.de>
 * @date      24.12.2018
 */

namespace FabianFroehlich\Core\Api\Controller;


use FabianFroehlich\Core\Api\Connection\ApiRequest;
use FabianFroehlich\Core\Api\Connection\JsonApiResponse;
use FabianFroehlich\Validator\Constraints\AbstractConstraint;

/**
 * Class AbstractApiController
 *
 * @package FabianFroehlich\Core\Api\Controller
 */
abstract class AbstractApiController {


    /** @var JsonApiResponse */
    protected $response;

    /** @var ApiRequest */
    protected $request;


    /**
     * @return JsonApiResponse
     */
    public function getResponse(): JsonApiResponse {

        return $this->response;
    }

    /**
     * @param JsonApiResponse $response
     */
    public function setResponse(JsonApiResponse $response): void {

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
     * Get the constraint of the Request
     *
     * @return AbstractConstraint
     */
    abstract public function getRequestConstraint(): AbstractConstraint;

    /**
     * Get the constraint of the Request
     *
     * @return AbstractConstraint
     */
    abstract public function getResponseConstraint(): AbstractConstraint;

}