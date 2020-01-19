<?php
/**
 * Copyright (c) 2020.
 *
 * Class ApiRequestExceptionTest.php
 *
 * @author      Fabian Fröhlich <mail@f-froehlich.de>
 *
 * @package     core-api
 * @since       Sun, Jan 19, '20
 */

namespace FabianFroehlich\Core\Api\Tests\Exception\Unit;


use FabianFroehlich\Core\Api\Exception\ApiRequestException;
use PHPUnit\Framework\TestCase;

class ApiRequestExceptionTest
    extends TestCase {

    /**
     * @test
     */
    public function exceptionIsThrown(): void {
        $this->expectException(ApiRequestException::class);

        throw new ApiRequestException();
    }

}