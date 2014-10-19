<?php

require 'vendor/autoload.php';

use React\Http\Request;
use React\Http\Response;
use React\EventLoop\Factory;
use React\Socket\Server as SocketServer;
use React\Http\Server as HttpServer;
use JMS\Serializer\SerializationContext;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

$routes = include 'routing.php';

$matcher = new UrlMatcher($routes, new RequestContext(''));

$repositoryRoot = __DIR__;
$projectRoot = __DIR__;

$serializer = \JMS\Serializer\SerializerBuilder::create()
    ->addMetadataDir($projectRoot.'/serializer')
    ->build();

$app = function (
    Request $request,
    Response $response
) use (
    $matcher,
    $repositoryRoot,
    $projectRoot,
    $serializer,
    $routes
) {
    try {
        $parameters = $matcher->match($request->getPath());
    } catch (ResourceNotFoundException $e) {
        $response->writeHead(404, array('Content-Type' => 'application/json'));
        $response->end(json_encode(['error' => 'Not Found']));
        return;
    }
    $controller = 'GitRest\\Controller\\'.$parameters['_controller'][0];
    $action = $parameters['_controller'][1];
    $callable = [$controller, $action];
    if (is_callable($callable)) {
        /** @var \GitRest\Controller\Controller $c */
        $c = new $controller();
        // TODO: checks!
        $c->setRepositoryRoot($repositoryRoot);
        $c->setProjectRoot($projectRoot);
        $reflection = new ReflectionClass($c);
        $actionMethod = $reflection->getMethod($action);
        $args = $actionMethod->getParameters();
        $params = [];
        foreach ($args as $arg) {
            if ($arg->getClass()) {
                if ('React\Http\Request' === $arg->getClass()->getName()) {
                    $params[] = $request;
                }
                if ('Symfony\Component\Routing\RouteCollection' === $arg->getClass()->getName()) {
                    $params[] = $routes;
                }
                continue;
            }
            if (array_key_exists($arg->getName(), $parameters)) {
                $params[] = $parameters[$arg->getName()];
            } else {
                $params[] = null;
            }
        }
        try {
            $data = call_user_func_array([$c, $action], $params);
        } catch (\Exception $e) {
            $data = ['error' => $e->getMessage()];
        }
        $response->writeHead(200, array('Content-Type' => 'application/json'));
        $response->end(
            $serializer->serialize(
                $data,
                'json',
                SerializationContext::create()->setGroups('list')
            )
        );
        return;
    } else {
        $response->writeHead(404, array('Content-Type' => 'application/json'));
        $response->end(json_encode(['error' => 'controller not found']));
    }
};

$loop = Factory::create();
$socket = new SocketServer($loop);
$http = new HttpServer($socket, $loop);

$http->on('request', $app);
echo "Server running at http://127.0.0.1:1337\n";

$socket->listen(1337);
$loop->run();