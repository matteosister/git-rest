<?php

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;

$routes = new RouteCollection();
$routes->add('home', new Route('/', ['_controller' => ['MainController', 'home']]));
$routes->add('status', new Route('/status', ['_controller' => ['GitController', 'status']]));

$context = new RequestContext('');
$matcher = new UrlMatcher($routes, $context);

return $matcher;