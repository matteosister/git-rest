<?php

namespace GitRest\Controller;

use GitRest\Exception\BadRequestException;
use React\Http\Request;
use React\Http\Response;

class GitController
{
    use Controller;

    public function status()
    {
        return $this->getRepository()->getStatus();
    }

    public function statusType($type)
    {
        $status = $this->getRepository()->getStatus();
        if (is_callable([$status, $type])) {
            return call_user_func([$status, $type]);
        }
        throw new BadRequestException;
    }

    public function tree(Request $request)
    {
        $query = $request->getQuery();
        $query = array_replace(['ref' => 'master', 'path' => null], $query);
        return $this->getRepository()->getTree($query['ref'], $query['path']);
    }

    public function blob(Request $request, Response $response)
    {
        $query = $request->getQuery();
        $query = array_replace(['ref' => 'master', 'path' => null], $query);
        $lines = $this->getRepository()->outputContent(
            $this->getRepository()->getTree($query['ref'], $query['path'])->getObject(),
            $query['ref']
        );
        $response->writeHead(200, [
            'Content-Type' => 'text/plain',
            'Access-Control-Allow-Origin' => '*'
        ]);
        $response->end(implode("\n", $lines));
        return;
    }

    public function branches()
    {
        return $this->getRepository()->getBranches();
    }
}
