<?php
/**
 * Copyright (c) 2020.
 *
 * Class JsonApiResponse.php
 *
 * @author      Fabian Fröhlich <mail@f-froehlich.de>
 *
 * @package     core-api
 * @since       Sat, Jan 18, '20
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
     * @var array
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
     *
     * @return array
     */
    public function getData(): array {

        return $this->rawData;
    }
}