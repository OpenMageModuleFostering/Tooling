<?php


namespace OpenMageModuleFostering\Tooling\Job;


use OpenMageModuleFostering\Tooling\Config;
use OpenMageModuleFostering\Tooling\RepositoryInfo;

abstract class AbstractJob
{

    protected $config;

    public function __construct()
    {
        $this->config = new Config();
    }


    abstract function execute(RepositoryInfo $repositoryInfo);
}
