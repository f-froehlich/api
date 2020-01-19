<?php
/**
 * Copyright (c) 2020.
 *
 * Class ApiRequestTest.php
 *
 * @author      Fabian Fröhlich <mail@f-froehlich.de>
 *
 * @package     core-api
 * @since       Sun, Jan 19, '20
 */

namespace FabianFroehlich\Core\Api\Tests\Connection\Unit;


use FabianFroehlich\Core\Api\Connection\ApiRequest;
use PHPUnit\Framework\TestCase;

class ApiRequestTest
    extends TestCase {

    /**
     * @var ApiRequest
     */
    private $request;

    public function setUp(): void {
        parent::setUp();
        $this->request = new ApiRequest();


    }

    /**
     * @test
     */
    public function params(): void {
        $params = 'params';

        $this->request->setParams($params);
        $this->assertSame($params, $this->request->getParams());
    }


}