<?php
/**
 * Copyright (c) 2020.
 *
 * Class ApiResponseExceptionTest.php
 *
 * @author      Fabian Fröhlich <mail@f-froehlich.de>
 *
 * @package     core-api
 * @since       Sun, Jan 19, '20
 */

namespace FabianFroehlich\Core\Api\Tests\Exception\Unit;


use FabianFroehlich\Core\Api\Exception\ApiResponseException;
use PHPUnit\Framework\TestCase;

class ApiResponseExceptionTest
    extends TestCase {

    /**
     * @test
     */
    public function exceptionIsThrown(): void {
        $this->expectException(ApiResponseException::class);

        throw new ApiResponseException();
    }

}