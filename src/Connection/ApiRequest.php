<?php

namespace FabianFroehlich\Core\Api\Connection;

use FabianFroehlich\Core\Api\Exception\ApiException;

/**
 * Class ApiRequest
 *
 * @package FabianFroehlich\Core\Api
 */
class ApiRequest {

    use ApiTrait;

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


    /**
     * {@inheritDoc}
     */
    public function validate(): void {

        $errors = $this->validator->validateRequest($this);
        if (0 !== $errors->count()) {
            // TODO
            var_dump($errors);
            throw new ApiException('Request is invalid!', ApiException::REQUEST_INVALID, []);
        }
    }

}