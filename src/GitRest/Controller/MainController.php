<?php

namespace GitRest\Controller;

use React\Http\Request;
use React\Http\Response;

class MainController
{
    use Controller;

    public function home(Request $request, Response $response)
    {
        $response->writeHead(200, array('Content-Type' => 'application/json'));
        $response->end('{}');
    }
} 