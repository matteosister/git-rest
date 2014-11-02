<?php

namespace GitRest;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use React\Http\Request;
use React\Http\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class Application
 */
class Application
{
    /**
     * @var RouteCollection
     */
    private $routes;

    /**
     * @var UrlMatcher
     */
    private $matcher;

    /**
     * @var string
     */
    private $repositoryRoot;

    /**
     * @var string
     */
    private $projectRoot;

    /**
     * @param RouteCollection $routes
     */
    public function __construct(RouteCollection $routes)
    {
        $this->routes = $routes;
        $this->matcher = new UrlMatcher($routes, new RequestContext(''));
    }

    public function setRepositoryRoot($repositoryRoot)
    {
        $this->repositoryRoot = $repositoryRoot;
    }

    public function setProjectRoot($projectRoot)
    {
        $this->projectRoot = $projectRoot;
    }

    protected function getSerializer()
    {
        return SerializerBuilder::create()
            ->addMetadataDir($this->projectRoot.'/serializer')
            ->build();
    }

    public function __invoke()
    {
        return function (Request $request, Response $response) {
            try {
                $parameters = $this->matcher->match($request->getPath());
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
                $c->setRepositoryRoot($this->repositoryRoot);
                $c->setProjectRoot($this->projectRoot);
                $reflection = new \ReflectionClass($c);
                $actionMethod = $reflection->getMethod($action);
                $args = $actionMethod->getParameters();
                $params = [];
                foreach ($args as $arg) {
                    if ($arg->getClass()) {
                        if ('React\Http\Request' === $arg->getClass()->getName()) {
                            $params[] = $request;
                        }
                        if ('React\Http\Response' === $arg->getClass()->getName()) {
                            $params[] = $response;
                        }
                        if ('Symfony\Component\Routing\RouteCollection' === $arg->getClass()->getName()) {
                            $params[] = $this->routes;
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
                if ($data) {
                    $response->writeHead(200, [
                        'Content-Type' => 'application/json',
                        'Access-Control-Allow-Origin' => '*'
                    ]);
                    $response->end(
                        $this->getSerializer()->serialize(
                            $data,
                            'json',
                            SerializationContext::create()
                                ->setGroups('list')
                                ->setSerializeNull(true)
                        )
                    );
                }
                return;
            } else {
                $response->writeHead(404, array('Content-Type' => 'application/json'));
                $response->end(json_encode(['error' => 'controller not found']));
            }
        };
    }
}
