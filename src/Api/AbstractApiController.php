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

declare(strict_types=1);
/**
 * @author    Fabian Fröhlich <mail@f-froehlich.de>
 * @date      24.12.2018
 */

namespace FabianFroehlich\Core\Api\Api;


use FabianFroehlich\Core\Api\Connection\ApiRequest;
use FabianFroehlich\Core\Api\Interfaces\ApiResponseInterface;
use FabianFroehlich\Validator\Constraints\AbstractConstraint;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class AbstractApiController
 *
 * @package FabianFroehlich\Core\Api\Controller
 */
abstract class AbstractApiController
    extends AbstractController {


    /** @var ApiResponseInterface */
    protected $response;

    /** @var ApiRequest */
    protected $request;


    /**
     * @return ApiResponseInterface
     */
    public function getResponse(): ApiResponseInterface {

        return $this->response;
    }

    /**
     * @param ApiResponseInterface $response
     */
    public function setResponse(ApiResponseInterface $response): void {

        $this->response = $response;
    }

    /**
     * @return ApiRequest
     */
    public function getRequest(): ApiRequest {

        return $this->request;
    }

    /**
     * @param ApiRequest $request
     */
    public function setRequest(ApiRequest $request): void {

        $this->request = $request;
    }

    /**
     * Get the constraint of the Request
     *
     * @return AbstractConstraint
     */
    abstract public function getRequestConstraint(): AbstractConstraint;

    /**
     * Get the constraint of the Request
     *
     * @return AbstractConstraint
     */
    abstract public function getResponseConstraint(): AbstractConstraint;

}