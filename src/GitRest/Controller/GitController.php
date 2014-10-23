<?php

namespace GitRest\Controller;

use GitRest\Exception\BadRequestException;

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
