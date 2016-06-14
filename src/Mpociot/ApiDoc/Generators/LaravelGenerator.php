<?php

namespace Mpociot\ApiDoc\Generators;

use Illuminate\Routing\Route;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;

class LaravelGenerator extends AbstractGenerator
{
    /**
     * @param Route $route
     *
     * @return mixed
     */
    protected function getUri($route)
    {
        return $route->getUri();
    }

    /**
     * @param  \Illuminate\Routing\Route $route
     * @param array $bindings
     * @param bool $withResponse
     *
     * @return array
     */
    public function processRoute($route, $bindings = [], $withResponse = true)
    {
        $response = '';

        if ($withResponse) {
            $response = $this->getRouteResponse($route, $bindings);
        }

        $routeAction = $route->getAction();
        $routeGroup = $this->getRouteGroup($routeAction['uses']);
        $routeDescription = $this->getRouteDescription($routeAction['uses']);

        if ($response->headers->get('Content-Type') === 'application/json') {
            $content = json_encode(json_decode($response->getContent()), JSON_PRETTY_PRINT);
        } else {
            $content = $response->getContent();
        }

        return $this->getParameters([
            'resource' => $routeGroup,
            'title' => $routeDescription['short'],
            'description' => $routeDescription['long'],
            'methods' => $route->getMethods(),
            'uri' => $route->getUri(),
            'parameters' => [],
            'response' => $content,
        ], $routeAction);
    }

    /**
     * Call the given URI and return the Response.
     *
     * @param  string  $method
     * @param  string  $uri
     * @param  array  $parameters
     * @param  array  $cookies
     * @param  array  $files
     * @param  array  $server
     * @param  string  $content
     *
     * @return \Illuminate\Http\Response
     */
    public function callRoute(
        $method,
        $uri,
        $parameters = [],
        $cookies = [],
        $files = [],
        $server = [],
        $content = null
    ) {
        App::instance('middleware.disable', true);

        $server = [
            'CONTENT_TYPE' => 'application/json',
            'Accept' => 'application/json',
        ];

        $request = Request::create(
            $uri,
            $method,
            $parameters,
            $cookies,
            $files,
            $this->transformHeadersToServerVars($server),
            $content
        );

        return app()->prepareResponse(
            app()->handle($request)
        );
    }
}
