<?php


namespace OpenMageModuleFostering\Tooling\Task;


use OpenMageModuleFostering\Tooling\Config;
use OpenMageModuleFostering\Tooling\RepositoryInfo;

abstract class AbstractTask
{
    protected $config;
    protected $repositoryInfo;

    public function __construct(
        RepositoryInfo $repositoryInfo,
        Config $config
    )
    {
        $this->config = $config;
        $this->repositoryInfo = $repositoryInfo;
    }


    abstract public function execute();
}
