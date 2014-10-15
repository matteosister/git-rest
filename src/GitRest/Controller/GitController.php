<?php

namespace GitRest\Controller;

use React\Http\Request;
use React\Http\Response;

class GitController
{
    use Controller;

    public function status(Request $request, Response $response)
    {
        $response->writeHead(200, array('Content-Type' => 'application/json'));
        $response->end($this->getSerializer()->serialize($this->getRepository()->getStatus(), 'json'));
    }
} 