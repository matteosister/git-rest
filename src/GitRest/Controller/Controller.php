<?php

namespace GitRest\Controller;

use GitElephant\Repository;
use JMS\Serializer\SerializerBuilder;

trait Controller
{
    /**
     * @var string
     */
    private $repositoryRoot;

    /**
     * @var string
     */
    private $projectRoot;

    /**
     * @param string $repositoryRoot
     */
    public function setRepositoryRoot($repositoryRoot)
    {
        $this->repositoryRoot = $repositoryRoot;
    }

    /**
     * @param string $projectRoot
     */
    public function setProjectRoot($projectRoot)
    {
        $this->projectRoot = $projectRoot;
    }

    /**
     * @return string
     */
    public function getProjectRoot()
    {
        return $this->projectRoot;
    }

    /**
     * @return Repository
     */
    protected function getRepository()
    {
        return new Repository($this->repositoryRoot);
    }

    /**
     * @return \JMS\Serializer\Serializer
     */
    protected function getSerializer()
    {
        return SerializerBuilder::create()
            ->addMetadataDir($this->getProjectRoot().'/serializer')
            ->build();
    }
} 