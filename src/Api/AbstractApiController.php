<?php
/**
 * Copyright (c) 2020.
 *
 * Class AbstractApiController.php
 *
 * @author      Fabian Fröhlich <mail@f-froehlich.de>
 *
 * @package     core-api
 * @since       Sun, Jan 19, '20
 */

declare(strict_types=1);
/**
 * @author    Fabian Fröhlich <mail@f-froehlich.de>
 * @date      24.12.2018
 */

namespace FabianFroehlich\Core\Api\Api;


use FabianFroehlich\Core\Api\Connection\ApiRequest;
use FabianFroehlich\Core\Api\Interfaces\ApiResponseInterface;
use FabianFroehlich\Validator\Constraints\AbstractConstraint;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class AbstractApiController
 *
 * @package FabianFroehlich\Core\Api\Controller
 */
abstract class AbstractApiController
    extends AbstractController {


    /** @var ApiResponseInterface */
    protected $response;

    /** @var ApiRequest */
    protected $request;


    /**
     * @return ApiResponseInterface
     */
    public function getResponse(): ApiResponseInterface {

        return $this->response;
    }

    /**
     * @param ApiResponseInterface $response
     */
    public function setResponse(ApiResponseInterface $response): void {

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