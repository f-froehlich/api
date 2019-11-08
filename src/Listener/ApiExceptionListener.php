<?php
declare(strict_types = 1);


namespace FabianFroehlich\Core\Api\Listener;

use Exception;
use FabianFroehlich\Core\Exception\ExceptionHandlerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ApiExceptionListener
 * @package FabianFroehlich\Core\Api\Listener
 */
class ApiExceptionListener {

    /** @const array - HTTP-Contenttypes die vom Exception Handler unterstützt werden. */
    private const SUPPORTED_CONTENT_TYPES = ['application/json', '*/*'];

    /** @var TranslatorInterface */
    private $translator;

    /** @var string */
    private $debug;

    /** @var ExceptionHandlerInterface[] */
    private $handlers = [];

    /**
     * ApiExceptionListener constructor.
     *
     * @param TranslatorInterface $translator
     * @param bool                $debug
     */
    public function __construct(TranslatorInterface $translator, bool $debug) {

        $this->translator = $translator;
        $this->debug      = $debug;
    }

    /**
     * @param GetResponseForExceptionEvent $event
     *
     * @throws \Symfony\Component\Translation\Exception\InvalidArgumentException
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function onException(GetResponseForExceptionEvent $event) : void {


        $request = $event->getRequest();

        // Nur Api-Anfragen behandeln.
        if (false === strpos($request->getRequestUri(), 'eLMS/Api')) { // TODO @FF

            return;
        }
        // HTTP-Contenttypen die der anfragende Client unterstützt.
        $acceptableContentTypes = $request->getAcceptableContentTypes();

        // Wenn der Client HTML-Ausgaben unterstützt und der Debug-Modus aktiviert ist, Symfony die Fehlerausgabe überlassen.
        if ($this->debug && in_array('text/html', $acceptableContentTypes, true)) {
            return;
        }

        // Rückgabetypen die von Handler und dem Clienten unterstützt werden bestimmen
        $availableContentTypes = array_intersect(
            $acceptableContentTypes,
            self::SUPPORTED_CONTENT_TYPES
        );

        // Falls der Client keinen Inhaltstypen akzeptiert, der von diesem Handler unterstützt wird, abbrechen.
        if (0 === count($availableContentTypes)) {
            return;
        }

        $event->setResponse($this->createResponse($event->getException(), $request));

    }

    /**
     * @param ExceptionHandlerInterface $handler
     */
    public function addHandler(ExceptionHandlerInterface $handler) : void {
        $this->handlers[$handler->getExceptionClassName()] = $handler;
    }

    /**
     * @param Exception $exception
     *
     * @param Request    $request
     *
     * @param Response   $response
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    private function compileExceptionData(Exception $exception, Request $request, Response $response) : array {

        $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);

        $data[] = [
            'message' => $this->translator->trans(
                'error.api.general_error',
                [
                    '%URL%' => $request->getRequestUri()
                ],
                'domain'
            )
        ];

        foreach ($this->handlers as $className => $handler) {
            if ($exception instanceof $className) {
                $data[] = $handler->handle($exception, $request, $response);
            }
        }

        return array_merge(...$data);

    }

    /**
     * Erstellt ein Array mit, zum Debuggen nützlichen, Daten
     *
     * @param Exception $exception
     * @param Request    $request
     *
     * @return array
     */
    private function compileDebugData(Exception $exception, Request $request) : array {
       return [
            'requestUri'             => $request->getRequestUri(),
            'exceptionClass'         => \get_class($exception),
            'message'                => $exception->getMessage(),
            'code'                   => $exception->getCode(),
            'line'                   => $exception->getLine(),
            'file'                   => $exception->getFile(),
            'stackTrace'             => $exception->getTraceAsString(),
            'previousExceptionClass' => $exception->getPrevious() === null ? null : \get_class($exception->getPrevious())
        ];

    }

    /**
     * Erstellt eine Response mit Informationen über die übergebene Ausnahme.
     *
     * @param Exception $exception
     * @param Request    $request
     *
     * @return Response
     * @throws \InvalidArgumentException
     */
    private function createResponse(Exception $exception, Request $request) : Response {

        $response = new JsonResponse();

        $data = $this->compileExceptionData($exception, $request, $response);

        if ($this->debug) {
            $data['debug'] = $this->compileDebugData($exception, $request);
        }

        $response->setData($data);

        return $response;
    }
}