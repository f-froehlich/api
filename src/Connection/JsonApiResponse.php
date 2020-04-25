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


namespace FabianFroehlich\Core\Api\Connection;


use Exception;
use FabianFroehlich\Core\Api\Interfaces\ApiResponseInterface;
use Symfony\Component\HttpFoundation\JsonResponse;


/**
 * Class ApiResponse
 *
 * @package FabianFroehlich\Core\Api\Connection
 */
class JsonApiResponse
    extends JsonResponse
    implements ApiResponseInterface {

    /**
     * @var mixed
     */
    private $rawData;

    /**
     * Sets the data to be sent.
     *
     * @param array $data
     *
     * @return $this
     *
     * @throws Exception
     */
    public function setData($data = []): self {

        $this->rawData = $data;

        parent::setData($data);

        return $this;
    }

    /**
     * Get the stored Data
     */
    public function getData() {

        return $this->rawData;
    }
}