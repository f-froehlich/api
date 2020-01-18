<?php
/**
 * Copyright (c) 2020.
 *
 * Class ApiTrait.php
 *
 * @author      Fabian Fröhlich <mail@f-froehlich.de>
 *
 * @package     core-api
 * @since       Sun, Jan 5, '20
 */

declare(strict_types=1);


namespace FabianFroehlich\Core\Api\Connection;


use FabianFroehlich\Core\Api\Exception\ApiException;
use FabianFroehlich\Core\Api\Service\AbstractValidatorService;

/**
 * Trait ApiTrait
 *
 * @package FabianFroehlich\Core\Api\Connection
 */
trait ApiTrait {


    /** @var AbstractValidatorService */
    private $validator;

    public function getValidator(): AbstractValidatorService {

        return $this->validator;
    }

    /**
     * @param AbstractValidatorService $validator
     */
    public function setValidator(AbstractValidatorService $validator): void {

        $this->validator = $validator;
    }

    /**
     * Validiert die Anfrage
     *
     * @return void
     * @throws ApiException - Wenn nicht valid
     */
    abstract public function validate(): void;

}