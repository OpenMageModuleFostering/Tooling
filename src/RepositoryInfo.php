<?php


namespace OpenMageModuleFostering\Tooling;


class RepositoryInfo
{
    protected $localDirectory;
    protected $name;

    public function __construct(
        $name,
        $localDirectory
    )
    {
        $this->localDirectory = $localDirectory;
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getLocalBaseDirectory()
    {
        return $this->localDirectory;
    }

    public function getLocalAbsolutePath($path)
    {
        return $this->localDirectory . '/' . $path;
    }

}
