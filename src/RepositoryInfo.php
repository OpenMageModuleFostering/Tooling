<?php


namespace OpenMageModuleFostering\Tooling;


class RepositoryInfo
{
    protected $localDirectory;
    protected $name;
    protected $archivedInfo;

    public function __construct(
        $name,
        $localDirectory,
        $archivedInfo = null
    )
    {
        $this->localDirectory = $localDirectory;
        $this->name = $name;
        $this->archivedInfo = $archivedInfo;
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

    public function isArchived()
    {
        return !is_null($this->archivedInfo);
    }

    public function getArchivedReplacementName()
    {
        return $this->archivedInfo['replacement_name'];
    }
    public function getArchivedReplacementUrl()
    {
        return $this->archivedInfo['replacement_url'];
    }

}
