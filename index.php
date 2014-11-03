<?php

$params = $argv;
array_shift($params);
if (!isset($params[0])) {
    $repoRoot = __DIR__;
} else {
    $repoRoot = $params[0];
}

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

$app = new \GitRest\Application($routes);
$app->setProjectRoot(__DIR__);
$app->setRepositoryRoot($repoRoot);

$loop = Factory::create();
$socket = new SocketServer($loop);
$http = new HttpServer($socket, $loop);

$http->on('request', $app());
echo "Server running at http://127.0.0.1:1337\n";

$socket->listen(1337);
$loop->run();