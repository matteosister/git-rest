<?php

namespace GitRest\Controller;

use React\Http\Request;

class GitController
{
    use Controller;

    public function status()
    {
        return $this->getRepository()->getStatus();
    }

    public function tree($ref, $path)
    {
        return $this->getRepository()->getTree($ref, $path);
    }

    public function blob($ref, $path)
    {
        return $this->getRepository()->outputContent(
            $this->getRepository()->getTree($ref, $path)->getObject(),
            $ref
        );
    }
} 