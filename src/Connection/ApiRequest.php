<?php
/**
 * Copyright (c) 2020.
 *
 * Class ApiRequest.php
 *
 * @author      Fabian Fröhlich <mail@f-froehlich.de>
 *
 * @package     core-api
 * @since       Sat, Jan 18, '20
 */

namespace FabianFroehlich\Core\Api\Connection;

/**
 * Class ApiRequest
 *
 * @package FabianFroehlich\Core\Api
 */
class ApiRequest {

    private $params;

    /**
     * @return mixed
     */
    public function getParams() {

        return $this->params;
    }

    /**
     * @param mixed $params
     */
    public function setParams($params): void {

        $this->params = $params;
    }

}