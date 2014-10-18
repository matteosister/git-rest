<?php

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;

$routes = new RouteCollection();
$routes->add('home', new Route('/', ['_controller' => ['MainController', 'home']]));
$routes->add('status', new Route('/status', ['_controller' => ['GitController', 'status']]));
$routes->add(
    'tree',
    new Route(
        '/tree/{ref}/{path}',
        [
            '_controller' => ['GitController', 'tree'],
            'path' => null
        ],
        [ 'path' => '.*' ]
    )
);

$routes->add(
    'blob',
    new Route(
        '/blob/{ref}/{path}',
        [
            '_controller' => ['GitController', 'blob'],
            'path' => null
        ],
        [ 'path' => '.*' ]
    )
);

$context = new RequestContext('');
$matcher = new UrlMatcher($routes, $context);

return $matcher;