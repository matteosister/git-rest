<?php

require 'vendor/autoload.php';

/** @var \Symfony\Component\Routing\Matcher\UrlMatcher $matcher */
$matcher = include 'routing.php';

$repositoryRoot = __DIR__;
$projectRoot = __DIR__;

$app = function (\React\Http\Request $request, \React\Http\Response $response) use ($matcher, $repositoryRoot, $projectRoot) {
    try {
        $parameters = $matcher->match($request->getPath());
    } catch (\Symfony\Component\Routing\Exception\ResourceNotFoundException $e) {
        $response->writeHead(404, array('Content-Type' => 'application/json'));
        $response->end(json_encode(['error' => 'Not Found']));
        return;
    }
    $controller = 'GitRest\\Controller\\'.$parameters['_controller'][0];
    $action = $parameters['_controller'][1];
    $callable = [$controller, $action];
    if (is_callable($callable)) {
        $c = new $controller();
        // TODO: checks!
        $c->setRepositoryRoot($repositoryRoot);
        $c->setProjectRoot($projectRoot);
        $c->$action($request, $response);
        return;
    } else {
        $response->writeHead(404, array('Content-Type' => 'application/json'));
        $response->end(json_encode(['error' => 'controller not found']));
    }
};

$loop = React\EventLoop\Factory::create();
$socket = new React\Socket\Server($loop);
$http = new React\Http\Server($socket, $loop);

$http->on('request', $app);
echo "Server running at http://127.0.0.1:1337\n";

$socket->listen(1337);
$loop->run();