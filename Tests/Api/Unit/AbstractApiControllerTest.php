<?php
/**
 * Copyright (c) 2020.
 *
 * Class AbstractApiControllerTest.php
 *
 * @author      Fabian Fröhlich <mail@f-froehlich.de>
 *
 * @package     core-api
 * @since       Sun, Jan 19, '20
 */

namespace FabianFroehlich\Core\Api\Tests\Api\Unit;


use FabianFroehlich\Core\Api\Api\AbstractApiController;
use FabianFroehlich\Core\Api\Connection\ApiRequest;
use FabianFroehlich\Core\Api\Interfaces\ApiResponseInterface;
use FabianFroehlich\Core\Api\Tests\Api\Fixtures\ApiController;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AbstractApiControllerTest
    extends TestCase {

    /**
     * @var MockObject|ApiRequest
     */
    private $request;
    /**
     * @var MockObject|ApiResponseInterface
     */
    private $response;
    /**
     * @var MockObject|AbstractApiController
     */
    private $controller;

    public function setUp(): void {
        parent::setUp();
        $this->request = $this->getMockBuilder(ApiRequest::class)
                              ->disableOriginalConstructor()
                              ->getMock();

        $this->response = $this->getMockBuilder(ApiResponseInterface::class)
                               ->disableOriginalConstructor()
                               ->getMockForAbstractClass();

        $this->controller = new ApiController();
    }

    /**
     * @test
     */
    public function request(): void {
        $this->controller->setRequest($this->request);
        $this->assertSame($this->request, $this->controller->getRequest());
    }

    /**
     * @test
     */
    public function response(): void {
        $this->controller->setResponse($this->response);
        $this->assertSame($this->response, $this->controller->getResponse());
    }

}