<?php

namespace HttpMessagesFractalMiddleware\Middleware;

use HttpMessagesFractalMiddleware\Services\ResponseService;
use HttpMessages\Http\Request;
use HttpMessages\Http\Response;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use HttpMessages\Exceptions\HttpMessagesException;

class FractalMiddleware
{
    /**
     * Response Service
     *
     * @var HttpMessagesFractalMiddleware\Services\ResponseService
     */
    protected $response_service;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->response_service = new ResponseService;
    }

    /**
     * Invoke
     *
     * @return void
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        try {
            $response = $this->response_service->getResponse($response);

            $response = $next($request, $response);

            $response = $this->applyFractal($request, $response);
        } catch (HttpMessagesException $exception) {
            $body = [
                'message' => $exception->getMessage(),
            ];

            if ($exception->getErrors()) {
                $body['errors'] = $exception->getErrors();
            }

            if ($exception->getInput()) {
                $body['input'] = $exception->getInput();
            }

            if (\Craft\craft()->config->get('devMode', 'HttpMessagesFractalMiddleware')) {
                $body['trace'] = $exception->getTrace();
            }

            $json = \Craft\JsonHelper::encode($body);

            $response = $response
                ->withStatus($exception->getStatusCode(), $exception->getStatusPhrase())
                ->withJson($json);
        } catch(\CException $exception) {
            $body = [
                'message' => $exception->getMessage(),
            ];

            if (\Craft\craft()->config->get('devMode', 'HttpMessagesFractalMiddleware')) {
                $body['trace'] = $exception->getTrace();
            }

            $json = \Craft\JsonHelper::encode($body);

            $response = $response
                ->withStatus(500)
                ->withJson($json);
        }

        return $response;
    }

    /**
     * Apply Fractal
     *
     * @param Request  $request  Request
     * @param Response $response Response
     *
     * @return Response Response
     */
    private function applyFractal(Request $request, Response $response)
    {
        $fractal = new Manager;
        $transformer = $this->getTransformer($request);

        // if (isset($this->includes)) {
        //     $this->manager->parseIncludes($this->includes);
        // }

        if ($response->getItem()) {
            $resource = new Item($response->getItem(), $transformer);
        }

        if (is_array($response->getCollection())) {
            $resource = new Collection($response->getCollection(), $transformer);

            if ($paginator = $this->getPaginator($response)) {
                $resource->setPaginator($paginator);
            }
        }

        if (isset($resource)) {
            $serializer = $this->getSerializer($request);
            $fractal->setSerializer($serializer);

            $resource = $fractal->createData($resource)->toArray();

            $json = \Craft\JsonHelper::encode($resource);

            $response = $response->withJson($json);
        }

        return $response;
    }

    /**
     * Get Transformer
     *
     * @param Request $request Request
     *
     * @return Transformer Transformer
     */
    private function getTransformer(Request $request)
    {
        $transformer = $request->getRoute()->getMiddlewareVariable('transformer', 'fractal');

        return new $transformer;
    }

    /**
     * Get Serializer
     *
     * @param Request $request Request
     *
     * @return Serializer Serializer
     */
    private function getSerializer(Request $request)
    {
        $serializers = \Craft\craft()->config->get('serializers', 'HttpMessagesFractalMiddleware');
        $default_serializer = \Craft\craft()->config->get('defaultSerializer', 'HttpMessagesFractalMiddleware');

        if (!isset($serializers[$default_serializer])) {
            $exception = new HttpMessagesException();
            $exception->setMessage(sprintf('The default serializer `%s` does not exist.', $default_serializer));
            throw $exception;
        }

        $serializer = $serializers[$default_serializer];

        return new $serializer;
    }

    /**
     * Get Paginator
     *
     * @param Response $response Response
     *
     * @return Paginator Paginator
     */
    private function getPaginator(Response $response)
    {
        $criteria = $response->getCriteria();

        if (!$criteria->limit) {
            return null;
        }

        $paginator = \Craft\craft()->config->get('paginator', 'HttpMessagesFractalMiddleware');

        return new $paginator($criteria);
    }

}
