<?php


namespace OpenMageModuleFostering\Tooling\Task;

/**
 *
 * fixes the wrong new lines in the description field
 *
 * Class ComposerJsonMultilineFix
 * @package OpenMageModuleFostering\Tooling\Task
 */
class ComposerJsonMultilineFix extends AbstractTask
{

    protected function getComposerJsonContent()
    {
        $composerJson = file_get_contents($this->repositoryInfo->getLocalAbsolutePath('composer.json'));
        $composerJsonLines = explode("\n", $composerJson);
        $replaceMode = false;
        $replacedLines = '';
        foreach ($composerJsonLines as $index => &$value) {
            if (strpos($value, '"description":') !== false) {
                $replaceMode = true;
            }
            if ($replaceMode === true) {
                if (strpos($value, '"authors":')) {
                    $replaceMode = false;
                    $value = $replacedLines . $value;
                } else {

                    if ($replacedLines !== '') {
                        $replacedLines .= '\n'; //add text line break, not real one
                    }
                    $replacedLines .= $value;
                    $value = '';
                }
            }
        }

        $composerJson = implode("\n", $composerJsonLines);
        $composerJsonObject = json_decode($composerJson, true);
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
