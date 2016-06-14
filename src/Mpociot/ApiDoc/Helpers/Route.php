<?php

namespace Mpociot\ApiDoc\Helpers;

/**
 *
 */
class Route
{
    protected $route;

    /**
     *
     */
    public function __construct(array $route)
    {
        $this->route = $route;
    }

    public function getName()
    {
        return (!isset($this->route['action']['as'])) ? "" : $this->route['as'];
    }

    public function getUri()
    {
        return  $this->route['uri'];
    }

    public function getAction()
    {
        return $this->route['action'];
    }

    public function getMethods()
    {
        if (is_array($this->route['method'])) {
            return $this->route['method'];
        }
        return [$this->route['method']];
    }
}
