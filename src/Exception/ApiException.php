<?php

declare(strict_types=1);

namespace FabianFroehlich\Core\Api\Exception;

use FabianFroehlich\Core\Exception\UserMessageException;


class ApiException extends UserMessageException {

    public const REQUEST_INVALID = 1;

    public const RESPONSE_INVALID = 2;

    public const VALIDATOR_NOT_EXIST = 4;

    public const MIMETYPE_INVALID = 8;

    /**
     * ApiException constructor.
     *
     * @param string $message
     * @param int    $code
     * @param array  $errors
     */
    public function __construct(string $message, int $code, array $errors) {

        parent::__construct($message, $code, null, [], 'FabianFroehlich', [], $errors);
    }

    /**
     * Returns the status code.
     *
     * @return int An HTTP response status code
     */
    public function getStatusCode(): int {

        return $this->code;
    }

}