<?php

namespace GitRest\Controller;

use PhpSpec\Locator\ResourceInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class MainController
{
    use Controller;

    public function home(RouteCollection $routeCollection)
    {
        $routes = [];
        /** @var Route $route */
        foreach ($routeCollection->getIterator() as $name => $route) {
            $routes[$name] = $route;
        }
        return $routes;
    }
}
