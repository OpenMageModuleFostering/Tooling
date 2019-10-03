<?php


namespace OpenMageModuleFostering\Tooling\Task;


class ComposerJsonKeywordsUpdate extends AbstractTask
{

    protected function getComposerJsonContent()
    {
        $composerJson = file_get_contents($this->repositoryInfo->getLocalAbsolutePath('composer.json'));
        $composerJsonObject = json_decode($composerJson, true);

        $composerJsonObject['keywords'] = [
            "openmage-module"
        ];

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
