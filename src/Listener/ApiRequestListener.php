<?php
/**
 * Copyright (c) 2020.
 *
 * Class ApiRequestListener.php
 *
 * @author      Fabian Fröhlich <mail@f-froehlich.de>
 *
 * @package     core-api
 * @since       Sun, Jan 19, '20
 */

declare(strict_types=1);


namespace FabianFroehlich\Core\Api\Listener;


use FabianFroehlich\Core\Api\Api\AbstractApiController;
use FabianFroehlich\Core\Api\Connection\ApiRequest;
use FabianFroehlich\Core\Api\Connection\JsonApiResponse;
use FabianFroehlich\Core\Api\Exception\ApiRequestException;
use FabianFroehlich\Core\Api\Exception\ApiResponseException;
use FabianFroehlich\Core\Api\Interfaces\ApiResponseInterface;
use FabianFroehlich\Validator\Constraints\AbstractConstraint;
use FabianFroehlich\Validator\Violation\ConstraintViolation;
use FabianFroehlich\Validator\Violation\DataConstraintViolationBuilder;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use function count;

class ApiRequestListener {

    /**
     * @var ApiResponseInterface
     */
    private $response;

    /**
     * @var AbstractApiController
     */
    private $controller;

    /**
     * @var ApiRequest
     */
    private $request;

    /**
     * @var DataConstraintViolationBuilder
     */
    private $constraintViolationBuilder;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * ApiRequestListener constructor.
     *
     * @param DataConstraintViolationBuilder $constraintViolationBuilder
     * @param LoggerInterface                $apiLogger
     */
    public function __construct(
        DataConstraintViolationBuilder $constraintViolationBuilder,
        LoggerInterface $apiLogger
    ) {
        $this->constraintViolationBuilder = $constraintViolationBuilder;
        $this->logger                     = $apiLogger;
    }


    /**
     * @param ControllerArgumentsEvent $event
     *
     * @throws ApiRequestException
     */
    public function prepareRequest(ControllerArgumentsEvent $event): void {

        if (!$event->isMasterRequest()) {
            return;
        }

        $this->controller = $event->getController()[0];

        if (!$this->controller instanceof AbstractApiController) {
            return;
        }

        $request       = $event->getRequest();
        $data          = $this->checkRequest($request);
        $this->request = new ApiRequest();
        $this->request->setParams($data);

        $this->controller->setResponse($this->response);
        $this->controller->setRequest($this->request);
    }

    /**
     * @param ResponseEvent $event
     *
     * @throws ApiResponseException
     */
    public function finishRequest(ResponseEvent $event): void {

        if (!$event->isMasterRequest()) {
            return;
        }

        $this->abortResponseIfModified($event);
        $this->validateResponse($event->getRequest());

    }

    /**
     * Abort the Request if Controller returned not the provided response Object
     *
     * @param ResponseEvent $event
     *
     * @throws ApiResponseException
     */
    private function abortResponseIfModified(ResponseEvent $event): void {
        if ($this->response !== $event->getResponse()) {
            $event->stopPropagation();

            $this->logger->debug(
                'You have to use $this->response instead of other creating a new Response Object. Aborting Request'
            );
            throw new ApiResponseException('Illegal Response Modification detected!');
        }
    }

    /**
     * Validate the Response. If Invalid throw exception
     *
     * @param Request $request
     *
     * @throws ApiResponseException
     */
    private function validateResponse(Request $request): void {
        $violations = $this->validate($this->response->getData(), $this->controller->getResponseConstraint());

        if (0 !== count($violations)) {

            $message = "Invalid API Response detected! Aborting Request.\n\tViolations:\n"
                       . $this->getLoggingMessage($this->response->getData(), $violations, $request);

            $this->logger->debug($message);
            $this->logger->debug(
                "Response does not match expected Constraints in Controller: '"
                . get_class($this->controller)
                . "'. Have the Definition changed?"
            );

            throw new ApiResponseException('Response is invalid.');
        }
    }

    /**
     * Validate the request
     *
     * @param Request $request
     *
     * @return array
     * @throws ApiRequestException
     */
    private function checkRequest(Request $request): array {
        $data       = $this->getDataOfRequest($request);
        $violations = $this->validate($data, $this->controller->getRequestConstraint());

        if (0 !== count($violations)) {

            $message = "Invalid API Request detected! Aborting Request.\tViolations:"
                       . $this->getLoggingMessage($data, $violations, $request);

            $this->logger->alert($message);
            $this->logger->debug(
                "Request does not match expected Constraints in Controller: '"
                . get_class($this->controller)
                . "'. Have the Definition changed?"
            );

            throw new ApiRequestException('Request is invalid.');
        }

        return $data;
    }

    /**
     * Getting the Data of the Request
     *
     * @param Request $request
     *
     * @return array
     * @throws ApiRequestException
     */
    private function getDataOfRequest(Request $request): array {

        $contentType = $request->getContentType();
        if (null === $contentType) {
            throw new ApiRequestException('Content type must be set.');
        }
        $mimeType = $request->getMimeType($contentType);

        $data = array_merge($request->request->all(), $request->query->all());

        switch ($mimeType) {
            case 'application/json':
                $this->response = new JsonApiResponse();

                return array_merge($data, (array)json_decode($request->getContent(), true));
            default:
                throw new ApiRequestException('Request Formant "' . $mimeType . '" is not for API');
        }
    }

    /**
     * Validate given Data with constraints and return violations
     *
     * @param array              $data
     *
     * @param AbstractConstraint $constraint
     *
     * @return ConstraintViolation[]
     */
    private function validate(array $data, AbstractConstraint $constraint): array {
        $this->constraintViolationBuilder->reset();
        return $this->constraintViolationBuilder->validateValue($data, $constraint)
                                                ->getViolations();
    }

    /**
     * Get the logging message for violations
     *
     * @param array   $data
     * @param array   $violations
     * @param Request $request
     *
     * @return string
     */
    private function getLoggingMessage(array $data, array $violations, Request $request): string {
        $message = '';
        foreach ($violations as $violation) {
            $message .= "\t\t" . $violation->getPropertyPath()
                        . "\t" . $violation->getCode()
                        . "\t" . $violation->getMessage()
                        . "\t" . $violation->getInvalidValue();
        }

        $message .= "\tData: " . implode(', ', $data);
        $message .= "\tUserInfo: " . $request->getUserInfo();
        $message .= "\tIps: [" . $request->getClientIp() . '] ' . implode(', ', $request->getClientIps());

        return $message;
    }

}