<?php

namespace GitRest\Controller;

use Cypress\PygmentsElephantBundle\PygmentsElephant\Pygmentize;
use Cypress\PygmentsElephantBundle\PygmentsElephant\PygmentizeBinary;
use GitRest\Exception\BadRequestException;
use React\Http\Request;
use React\Http\Response;
use GitRest\Response\Data;

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
        if (array_key_exists('pygmentize', $query)) {
            return Data::create($this->pygmentize($query['ref'], $query['path']))
                ->setHeader('Content-Type', 'text/html');
        } else {
            $object = $this->getRepository()->getTree($query['ref'], $query['path'])->getObject();
            if (is_null($object)) {
                throw new BadRequestException;
            }
            $lines = $this->getRepository()->outputContent($object, $query['ref']);
            return $lines;
        }
    }

    private function pygmentize($ref, $path)
    {
        $object = $this->getRepository()->getTree($ref, $path)->getObject();
        $path = $object->getRepository()->getPath().'/'.$object->getFullPath();
        $pygmentizeBinary = new PygmentizeBinary();
        $pygmentize = new Pygmentize($pygmentizeBinary);
        $pygmentize->setFormat('style=colorful,linenos=1');
        return $pygmentize->formatFile($path);
    }

    public function branches()
    {
        return $this->getRepository()->getBranches();
    }

    /**
     * @SerializationGroup("detail")
     */
    public function commit($sha)
    {
        $commit = $this->getRepository()->getCommit($sha);
        return Data::create($this->getRepository()->getDiff($commit))
            ->setSerializationGroup('detail');
    }
}
