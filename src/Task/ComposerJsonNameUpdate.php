<?php


namespace OpenMageModuleFostering\Tooling\Task;


class ComposerJsonNameUpdate extends AbstractTask
{

    protected function getComposerJsonContent()
    {
        $composerJson = file_get_contents($this->repositoryInfo->getLocalAbsolutePath('composer.json'));
        $composerJsonObject = json_decode($composerJson, true);

        $composerJsonObject['name'] = 'openmage-module-fostering/'.$this->repositoryInfo->getName();

        $composerJson = json_encode($composerJsonObject, JSON_PRETTY_PRINT);
        return $composerJson;
    }

    public function execute()
    {
        file_put_contents(
            $this->repositoryInfo->getLocalAbsolutePath('composer.json'),
            $this->getComposerJsonContent()
        );
    }
}
