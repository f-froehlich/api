<?php
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

    /**
     * @param AbstractValidatorService $validator
     */
    public function setValidator(AbstractValidatorService $validator): void {

        $this->validator = $validator;
    }

    public function getValidator(): AbstractValidatorService {

        return $this->validator;
    }

    /**
     * Validiert die Anfrage
     *
     * @return void
     * @throws ApiException - Wenn nicht valid
     */
    abstract public function validate(): void;

}