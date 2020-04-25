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

namespace FabianFroehlich\Core\Api\Tests\Listener\Unit;


use ArrayAccess;
use FabianFroehlich\Core\Api\Api\AbstractApiController;
use FabianFroehlich\Core\Api\Connection\JsonApiResponse;
use FabianFroehlich\Core\Api\Exception\ApiRequestException;
use FabianFroehlich\Core\Api\Exception\ApiResponseException;
use FabianFroehlich\Core\Api\Listener\ApiRequestListener;
use FabianFroehlich\Validator\Constraints\AbstractConstraint;
use FabianFroehlich\Validator\Constraints\IsBool;
use FabianFroehlich\Validator\Violation\ConstraintViolation;
use FabianFroehlich\Validator\Violation\DataConstraintViolationBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class ApiRequestListenerTest
    extends TestCase {

    /**
     * @var JsonApiResponse|ReflectionClass
     */
    private $listener;
    /**
     * @var MockObject|DataConstraintViolationBuilder
     */
    private $violationBuilder;
    /**
     * @var MockObject|LoggerInterface
     */
    private $logger;
    /**
     * @var MockObject|ControllerArgumentsEvent
     */
    private $requestEvent;
    /**
     * @var MockObject|AbstractApiController
     */
    private $controller;
    /**
     * @var MockObject|Request
     */
    private $request;
    /**
     * @var MockObject
     */
    private $requestQuery;
    /**
     * @var MockObject
     */
    private $requestParameter;

    /**
     * @var MockObject|ConstraintViolation
     */
    private $violation;
    /**
     * @var MockObject|ResponseEvent
     */
    private $responseEvent;
    /** @var MockObject|JsonApiResponse */
    private $response;

    public function setUp(): void {
        parent::setUp();

        $this->violationBuilder = $this->getMockBuilder(DataConstraintViolationBuilder::class)
                                       ->disableOriginalConstructor()
                                       ->getMock();

        $this->logger = $this->getMockBuilder(LoggerInterface::class)
                             ->disableOriginalConstructor()
                             ->getMockForAbstractClass();

        $this->requestEvent = $this->getMockBuilder(ControllerArgumentsEvent::class)
                                   ->disableOriginalConstructor()
                                   ->getMock();

        $this->responseEvent = $this->getMockBuilder(ResponseEvent::class)
                                    ->disableOriginalConstructor()
                                    ->getMock();

        $this->response = $this->getMockBuilder(JsonApiResponse::class)
                               ->disableOriginalConstructor()
                               ->getMock();


        $this->controller = $this->getMockBuilder(AbstractApiController::class)
                                 ->disableOriginalConstructor()
                                 ->getMockForAbstractClass();

        $this->requestParameter = $this->getMockBuilder(ParameterBag::class)
                                       ->disableOriginalConstructor()
                                       ->getMock();

        $this->requestQuery = $this->getMockBuilder(ParameterBag::class)
                                   ->disableOriginalConstructor()
                                   ->getMock();

        $this->request = $this->getMockBuilder(Request::class)
                              ->disableOriginalConstructor()
                              ->getMock();

        $this->request->query   = $this->requestQuery;
        $this->request->request = $this->requestParameter;

        $this->violation = $this->getMockBuilder(ConstraintViolation::class)
                                ->disableOriginalConstructor()
                                ->getMock();


        $this->listener = new ApiRequestListener($this->violationBuilder, $this->logger);


    }

    /**
     * @test
     */
    public function prepareRequestWillDoNothingIfIsNotTheMasterRequest(): void {
        $this->requestEvent->expects($this->once())
                           ->method('isMasterRequest')
                           ->willReturn(false);
        $this->requestEvent->expects($this->never())
                           ->method('getController');

        $this->listener->prepareRequest($this->requestEvent);

    }

    /**
     * @test
     */
    public function prepareRequestWillDoNothingIfIsNotApiController(): void {
        $this->passMasterRequest();
        $controller = $this->getMockBuilder(Request::class)
                           ->disableOriginalConstructor()
                           ->getMock();

        $callback = $this->getController($controller);
        $this->requestEvent->expects($this->once())
                           ->method('getController')
                           ->willReturn($callback);

        $this->listener->prepareRequest($this->requestEvent);

    }

    /**
     * Pass Master Request
     */
    private function passMasterRequest(): void {
        $this->requestEvent->expects($this->once())
                           ->method('isMasterRequest')
                           ->willReturn(true);
    }

    /**
     * Get the controller as array callable
     *
     * @param $controller
     *
     * @return callable
     */
    private function getController($controller): callable {
        return new class($controller)
            implements ArrayAccess {
            private $controller;

            public function __construct($controller) {

                $this->controller = $controller;
            }

            public function __invoke($name) {
                return [$this->controller];
            }

            /**
             * @inheritDoc
             */
            public function offsetExists($offset) {
                return $offset === 0;
            }

            /**
             * @inheritDoc
             */
            public function offsetGet($offset) {
                return $offset === 0 ? $this->controller : null;
            }

            public function offsetSet($offset, $value) {
            }

            public function offsetUnset($offset) {
            }
        };
    }

    /**
     * @test
     */
    public function prepareRequestWillThrowExceptionIfNoContentTypeSet(): void {
        $this->passController();
        $this->passContentType(null, null);
        $this->expectException(ApiRequestException::class);
        $this->expectExceptionMessage('Content type must be set.');

        $this->listener->prepareRequest($this->requestEvent);

    }

    /**
     * Pass valid controller
     */
    private function passController(): void {
        $this->passMasterRequest();
        $callback = $this->getController($this->controller);
        $this->requestEvent->expects($this->once())
                           ->method('getController')
                           ->willReturn($callback);

        $this->requestEvent->expects($this->once())
                           ->method('getRequest')
                           ->willReturn($this->request);
    }

    /**
     * Passing content type
     *
     * @param string|null $contentType
     */
    private function passContentType(?string $contentType, ?string $mimeType): void {

        $this->request->expects($this->once())
                      ->method('getContentType')
                      ->willReturn($contentType);

        if (null !== $mimeType) {
            $this->request->expects($this->once())
                          ->method('getMimeType')
                          ->with($contentType)
                          ->willReturn($mimeType);
        }
    }

    /**
     * @test
     */
    public function prepareRequestWillThrowExceptionIfMimeTypeIsNotForApi(): void {
        $this->passController();
        $this->passContentType('contentType', 'unknown');
        $this->passData([], [], null);
        $this->expectException(ApiRequestException::class);
        $this->expectExceptionMessage('Request Formant "unknown" is not for API');

        $this->listener->prepareRequest($this->requestEvent);

    }

    /**
     * Pass Data to request
     *
     * @param array $requestData
     * @param array $queryData
     * @param       $content
     */
    private function passData(array $requestData, array $queryData, $content): void {

        $this->requestParameter->expects($this->once())
                               ->method('all')
                               ->willReturn($requestData);

        $this->requestQuery->expects($this->once())
                           ->method('all')
                           ->willReturn($queryData);

        if (null !== $content) {
            $this->request->expects($this->once())
                          ->method('getContent')
                          ->willReturn(json_encode($content));
        }
    }

    /**
     * @test
     */
    public function prepareRequestWillThrowExceptionIfThereIsAViolation(): void {

        $constraint = new IsBool();
        $this->controller->expects($this->once())
                         ->method('getRequestConstraint')
                         ->willReturn($constraint);

        $this->request->expects($this->once())
                      ->method('getClientIps')
                      ->willReturn([]);

        $this->logger->expects($this->once())
                     ->method('alert');

        $this->logger->expects($this->once())
                     ->method('debug');


        $this->passController();
        $this->passContentType('json', 'application/json');
        $this->passData([], [], []);

        $this->passValidation([], [$this->violation], $constraint);
        $this->expectException(ApiRequestException::class);
        $this->expectExceptionMessage('Request is invalid.');

        $this->listener->prepareRequest($this->requestEvent);

    }

    /**
     *
     * Pass the validation
     *
     * @param array              $data
     * @param array              $violations
     * @param AbstractConstraint $constraint
     */
    private function passValidation(array $data, array $violations, AbstractConstraint $constraint): void {
        $this->violationBuilder->expects($this->once())
                               ->method('reset');

        $this->violationBuilder->expects($this->once())
                               ->method('validateValue')
                               ->with($data, $constraint)
                               ->willReturn(true);
        $this->violationBuilder->expects($this->once())
                               ->method('getViolations')
                               ->willReturn($violations);

    }

    /**
     * @test
     */
    public function prepareRequestWillPass(): void {

        $constraint = new IsBool();
        $this->controller->expects($this->once())
                         ->method('getRequestConstraint')
                         ->willReturn($constraint);

        $this->logger->expects($this->never())
                     ->method('alert');

        $this->logger->expects($this->never())
                     ->method('debug');


        $this->passController();
        $this->passContentType('json', 'application/json');
        $this->passData([], [], []);

        $this->passValidation([], [], $constraint);

        $this->listener->prepareRequest($this->requestEvent);

    }

    /**
     * @test
     */
    public function finishRequestWillDoNothingIfIsNotTheMasterRequest(): void {

        $listener = new ReflectionClass(ApiRequestListener::class);
        $property = $listener->getProperty('isApiRequest');
        $property->setAccessible(true);
        $property->setValue($this->listener, true);

        $this->responseEvent->expects($this->once())
                            ->method('isMasterRequest')
                            ->willReturn(false);

        $this->listener->finishRequest($this->responseEvent);

    }

    /**
     * @test
     */
    public function finishRequestWillDoNothingIfResponseIsValid(): void {

        $listener     = new ReflectionClass(ApiRequestListener::class);
        $isApiRequest = $listener->getProperty('isApiRequest');
        $isApiRequest->setAccessible(true);
        $isApiRequest->setValue($this->listener, true);

        $response = $listener->getProperty('response');
        $response->setAccessible(true);
        $response->setValue($this->listener, $this->response);

        $controller = $listener->getProperty('controller');
        $controller->setAccessible(true);
        $controller->setValue($this->listener, $this->controller);


        $this->responseEvent->expects($this->once())
                            ->method('isMasterRequest')
                            ->willReturn(true);
        $this->responseEvent->expects($this->once())
                            ->method('getResponse')
                            ->willReturn($this->response);
        $this->responseEvent->expects($this->once())
                            ->method('getRequest')
                            ->willReturn($this->request);
        $this->response->expects($this->once())
                       ->method('getData')
                       ->willReturn([]);
        $this->controller->expects($this->once())
                         ->method('getResponseConstraint')
                         ->willReturn(new IsBool());

        $this->violationBuilder->expects($this->once())
                               ->method('validateValue')
                               ->willReturn(true);

        $this->violationBuilder->expects($this->once())
                               ->method('getViolations')
                               ->willReturn([]);

        $this->listener->finishRequest($this->responseEvent);

    }

    /**
     * @test
     */
    public function finishRequestWillThrowExceptionIfResponseIsModified(): void {

        /** @var ReflectionClass|ApiRequestListener $listener */
        $listener = new ReflectionClass(ApiRequestListener::class);
        $method   = $listener->getMethod('abortResponseIfModified');
        $method->setAccessible(true);

        $this->logger->expects($this->once())
                     ->method('debug');

        $this->expectException(ApiResponseException::class);
        $this->expectExceptionMessage('Illegal Response Modification detected!');

        $method->invoke($this->listener, $this->responseEvent);

    }

    /**
     * @test
     */
    public function validateResponseWillPass(): void {

        /** @var ReflectionClass|ApiRequestListener $listener */
        $listener = new ReflectionClass(ApiRequestListener::class);
        $method   = $listener->getMethod('abortResponseIfModified');
        $method->setAccessible(true);
        $property = $listener->getProperty('response');
        $property->setAccessible(true);
        $property->setValue($this->listener, $this->response);

        $this->responseEvent->expects($this->once())
                            ->method('getResponse')
                            ->willReturn($this->response);


        $method->invoke($this->listener, $this->responseEvent);

    }

    /**
     * @test
     */
    public function checkRequestWillThrowException(): void {

        /** @var ReflectionClass|ApiRequestListener $listener */
        $listener = new ReflectionClass(ApiRequestListener::class);
        $method   = $listener->getMethod('validateResponse');
        $method->setAccessible(true);
        $property = $listener->getProperty('response');
        $property->setAccessible(true);
        $property->setValue($this->listener, $this->response);

        $controllerProperty = $listener->getProperty('controller');
        $controllerProperty->setAccessible(true);
        $controllerProperty->setValue($this->listener, $this->controller);

        $this->response->expects($this->exactly(2))
                       ->method('getData')
                       ->willReturn(['data']);

        $constraint = new IsBool();
        $this->controller->expects($this->once())
                         ->method('getResponseConstraint')
                         ->willReturn($constraint);


        $this->passValidation(['data'], [$this->violation], $constraint);

        $this->request->expects($this->once())
                      ->method('getClientIps')
                      ->willReturn([]);

        $this->logger->expects($this->exactly(2))
                     ->method('debug');

        $this->expectException(ApiResponseException::class);
        $this->expectExceptionMessage('Response is invalid.');


        $method->invoke($this->listener, $this->request);

    }

    /**
     * @test
     */
    public function checkRequestWillPass(): void {

        /** @var ReflectionClass|ApiRequestListener $listener */
        $listener = new ReflectionClass(ApiRequestListener::class);
        $method   = $listener->getMethod('validateResponse');
        $method->setAccessible(true);
        $property = $listener->getProperty('response');
        $property->setAccessible(true);
        $property->setValue($this->listener, $this->response);

        $controllerProperty = $listener->getProperty('controller');
        $controllerProperty->setAccessible(true);
        $controllerProperty->setValue($this->listener, $this->controller);

        $this->response->expects($this->once())
                       ->method('getData')
                       ->willReturn(['data']);

        $constraint = new IsBool();
        $this->controller->expects($this->once())
                         ->method('getResponseConstraint')
                         ->willReturn($constraint);


        $this->passValidation(['data'], [], $constraint);

        $method->invoke($this->listener, $this->request);

    }


}