<?php

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$routes = new RouteCollection();
$routes->add('home', new Route('/', ['_controller' => ['MainController', 'home']]));
$routes->add('status', new Route('/status', ['_controller' => ['GitController', 'status']]));
$routes->add('status_type', new Route('/status/{type}', ['_controller' => ['GitController', 'statusType']]));
$routes->add(
    'tree',
    new Route(
        '/tree',
        [
            '_controller' => ['GitController', 'tree'],
            'path' => null
        ]
    )
);

$routes->add(
    'blob',
    new Route(
        '/blob',
        [
            '_controller' => ['GitController', 'blob'],
            'path' => null
        ]
    )
);
$routes->add('branches', new Route('/branches', ['_controller' => ['GitController', 'branches']]));

return $routes;