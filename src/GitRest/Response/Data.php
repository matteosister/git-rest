<?php

namespace GitRest\Response;

class Data
{
    private $content;

    private $serializationGroup;

    /**
     * @param $content
     */
    private function __construct($content)
    {
        $this->content = $content;
        $this->serializationGroup = 'list';
    }

    public static function create($content)
    {
        return new self($content);
    }

    /**
     * @return string
     */
    public function getSerializationGroup()
    {
        return $this->serializationGroup;
    }

    /**
     * @param string $serializationGroup
     * @return $this
     */
    public function setSerializationGroup($serializationGroup)
    {
        $this->serializationGroup = $serializationGroup;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }
}
