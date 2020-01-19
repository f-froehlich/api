<?php
/**
 * Copyright (c) 2020.
 *
 * Class JsonApiResponseTest.php
 *
 * @author      Fabian Fröhlich <mail@f-froehlich.de>
 *
 * @package     core-api
 * @since       Sun, Jan 19, '20
 */

namespace FabianFroehlich\Core\Api\Tests\Connection\Unit;


use FabianFroehlich\Core\Api\Connection\JsonApiResponse;
use PHPUnit\Framework\TestCase;

class JsonApiResponseTest
    extends TestCase {

    /**
     * @var JsonApiResponse
     */
    private $response;

    public function setUp(): void {
        parent::setUp();
        $this->response = new JsonApiResponse();
    }

    /**
     * @test
     */
    public function params(): void {
        $params = 'params';

        $this->response->setData($params);
        $this->assertSame($params, $this->response->getData());
        $this->assertEquals(json_encode($params), $this->response->getContent());
    }


}