<?php
/**
 * Copyright (c) 2020.
 *
 * Class ApiRequestListener.php
 *
 * @author      Fabian Fröhlich <mail@f-froehlich.de>
 *
 * @package     core-api
 * @since       Sun, Jan 5, '20
 */

declare(strict_types=1);


namespace FabianFroehlich\Core\Api\Listener;


use FabianFroehlich\Core\Api\Connection\ApiRequest;
use FabianFroehlich\Core\Api\Connection\ApiResponse;
use FabianFroehlich\Core\Api\Controller\AbstractApiController;
use FabianFroehlich\Core\Api\Exception\ApiException;
use FabianFroehlich\Core\Api\Service\AbstractValidatorService;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class ApiRequestListener {

    private $isApiRequest = false;

    /**
     * @var ApiResponse
     */
    private $response;

    /**
     * @var ApiRequest
     */
    private $request;

    /**
     * @var Container
     */
    private $container;

    public function __construct(Container $container) {

        $this->container = $container;
    }


    /**
     * @param ControllerArgumentsEvent $event
     *
     * @throws ApiException
     */
    public function prepareRequest(ControllerArgumentsEvent $event): void {

        $controller = $event->getController()[0];

        if (!$controller instanceof AbstractApiController) {
            return;
        }


        $method         = $event->getController()[1];
        $validatorClass = $controller->getValidatorPrefix() . '\\' . ucfirst($method) . 'Validator';
        $request        = $event->getRequest();

        if (!$this->container->has($validatorClass)) {
            throw new ApiException($validatorClass . ' Does not exist!', ApiException::VALIDATOR_NOT_EXIST, []);
        }

        /** @var AbstractValidatorService $validator */
        $validator = $this->container->get($validatorClass);


        $this->request = new ApiRequest();


        $data        = array_merge($request->request->all(), $request->query->all());
        $contentType = $request->getMimeType($request->getContentType());

        switch ($contentType) {
            case 'application/json':
                $data = array_merge($data, (array)json_decode($request->getContent(), true));
                break;
            default:
                throw new ApiException(
                    'Request Formant "' . $contentType . '"is not for API',
                    ApiException::MIMETYPE_INVALID,
                    ['requestType' => $request->getContentType()]
                );
        }


        $this->request->setParams($data);

        $this->request->setValidator($validator);
        $this->request->validate();

        $this->response = new ApiResponse();
        $this->response->setValidator($validator);
        $controller->setResponse($this->response);
        $controller->setRequest($this->request);
        $this->isApiRequest = true;


    }

    /**
     * @param ResponseEvent $event
     *
     * @throws ApiException
     */
    public function finishRequest(ResponseEvent $event) {

        if ($this->isApiRequest) {
            $this->isApiRequest = false;
            $this->response->validate();
        }

    }

}