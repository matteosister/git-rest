<?php

require 'vendor/autoload.php';

/** @var \Symfony\Component\Routing\Matcher\UrlMatcher $matcher */
$matcher = include 'routing.php';

$repositoryRoot = __DIR__;
$projectRoot = __DIR__;

$serializer = \JMS\Serializer\SerializerBuilder::create()
    ->addMetadataDir($projectRoot.'/serializer')
    ->build();

$app = function (
    \React\Http\Request $request,
    \React\Http\Response $response
) use (
    $matcher,
    $repositoryRoot,
    $projectRoot,
    $serializer
) {
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
        $reflection = new ReflectionClass($c);
        $actionMethod = $reflection->getMethod($action);
        $args = $actionMethod->getParameters();
        $params = [];
        foreach ($args as $arg) {
            if ($arg->getClass() && 'React\Http\Request' === $arg->getClass()->getName()) {
                $params[] = $request;
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
                \JMS\Serializer\SerializationContext::create()->setGroups('list')
            )
        );
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