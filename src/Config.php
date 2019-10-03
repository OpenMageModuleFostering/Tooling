<?php


namespace OpenMageModuleFostering\Tooling;


class Config
{

    protected $configData;

    public function __construct()
    {
        $this->configData = require __DIR__ . '/../config.php';
    }

    public function getByPath($path)
    {
        return $this->configData[$path];
    }
}
