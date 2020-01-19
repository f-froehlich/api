<?php
/**
 * Copyright (c) 2020.
 *
 * Class ApiController.php
 *
 * @author      Fabian Fröhlich <mail@f-froehlich.de>
 *
 * @package     core-api
 * @since       Sun, Jan 19, '20
 */

namespace FabianFroehlich\Core\Api\Tests\Api\Fixtures;


use FabianFroehlich\Core\Api\Api\AbstractApiController;
use FabianFroehlich\Validator\Constraints\AbstractConstraint;
use FabianFroehlich\Validator\Constraints\IsBool;

class ApiController
    extends AbstractApiController {

    /**
     * @inheritDoc
     */
    public function getRequestConstraint(): AbstractConstraint {
        return new IsBool();
    }

    /**
     * @inheritDoc
     */
    public function getResponseConstraint(): AbstractConstraint {
        return new IsBool();
    }
}