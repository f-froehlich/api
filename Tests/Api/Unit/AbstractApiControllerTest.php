<?php
/**
 * API
 *
 * API Extension for validating each Request
 *
 * Copyright (c) 2020 Fabian Fröhlich <mail@f-froehlich.de> https://f-froehlich.de
 *
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 * For all license terms see README.md and LICENSE Files in root directory of this Project.
 *
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