<?php

namespace GitRest\Response;

class Data
{
    private $content;

    private $serializationGroup;

    private $defaultHeaders;

    /**
     * @param $content
     */
    private function __construct($content)
    {
        $this->content = $content;
        $this->serializationGroup = 'list';
        $this->defaultHeaders = [
            'Content-Type' => 'application/json',
            'Access-Control-Allow-Origin' => '*'
        ];
    }

    /**
     * @param $name
     * @param $value
     *
     * @return $this
     */
    public function setHeader($name, $value)
    {
        $this->defaultHeaders = array_replace($this->defaultHeaders, [$name => $value]);

        return $this;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getHeader($name)
    {
        return $this->defaultHeaders[$name];
    }

    public function isJson()
    {
        return $this->getHeader('Content-Type') === 'application/json';
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->defaultHeaders;
    }

    /**
     * @param $content
     * @return Data
     */
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
