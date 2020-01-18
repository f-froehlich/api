<?php
/**
 * Copyright (c) 2020.
 *
 * Class ApiExceptionTest.php
 *
 * @author      Fabian Fröhlich <mail@f-froehlich.de>
 *
 * @package     core-api
 * @since       Sat, Jan 18, '20
 */

namespace FabianFroehlich\Core\Api\Tests\Exception\Unit;


use FabianFroehlich\Core\Api\Exception\ApiException;
use PHPUnit\Framework\TestCase;

class ApiExceptionTest
    extends TestCase {

    /**
     * @test
     */
    public function exceptionIsThrown(): void {
        $this->expectException(ApiException::class);

        throw new ApiException();
    }

}