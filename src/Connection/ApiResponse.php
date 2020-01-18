<?php
/**
 * Copyright (c) 2020.
 *
 * Class ApiResponse.php
 *
 * @author      Fabian Fröhlich <mail@f-froehlich.de>
 *
 * @package     core-api
 * @since       Sun, Jan 5, '20
 */

declare(strict_types=1);


namespace FabianFroehlich\Core\Api\Connection;


use Exception;
use FabianFroehlich\Core\Api\Exception\ApiException;
use Symfony\Component\HttpFoundation\JsonResponse;


/**
 * Class ApiResponse
 *
 * @package FabianFroehlich\Core\Api\Connection
 */
class ApiResponse
    extends JsonResponse {


    use ApiTrait;

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

    /**
     * {@inheritDoc}
     */
    public function validate() {

        $errors = $this->validator->validateResponse($this);
        if (0 !== $errors->count()) {
            // TODO
            var_dump($errors);
            throw new ApiException('Response is invalid!', ApiException::RESPONSE_INVALID, []);
        }
    }

}