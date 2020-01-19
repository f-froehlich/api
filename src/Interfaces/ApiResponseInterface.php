<?php
/**
 * Copyright (c) 2020.
 *
 * Class ApiResponseInterface.php
 *
 * @author      Fabian Fröhlich <mail@f-froehlich.de>
 *
 * @package     core-api
 * @since       Sun, Jan 19, '20
 */

namespace FabianFroehlich\Core\Api\Interfaces;


interface ApiResponseInterface {

    public function getData();

    public function setData($data = []);


}