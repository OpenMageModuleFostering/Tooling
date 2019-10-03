<?php


namespace OpenMageModuleFostering\Tooling\Task;


class ComposerJsonLicenseFix extends AbstractTask
{

    protected function getComposerJsonContent()
    {
        $composerJson = file_get_contents($this->repositoryInfo->getLocalAbsolutePath('composer.json'));
        $composerJsonObject = json_decode($composerJson, true);

        $packageXmlContent = file_get_contents($this->repositoryInfo->getLocalAbsolutePath('package.xml'));

        if ($composerJsonObject['license'] === 'Apache') {
            if (strpos($packageXmlContent, '<license uri="http://www.apache.org/licenses/LICENSE-2.0">Apache</license>') !== false) {
                $composerJsonObject['license'] = 'Apache-2.0';
            }
        }
        if (in_array($composerJsonObject['license'], [
            'OSL',
            'Open-Software-License-(OSL)',
        ])) {
            if (strpos($packageXmlContent, '<license uri="https://github.com/Zookal/magento-mock">OSL</license>') !== false) {
                $composerJsonObject['license'] = 'OSL-3.0';
            }
            if (strpos($packageXmlContent, '<license uri="http://opensource.org/licenses/osl-3.0.php">') !== false) {
                $composerJsonObject['license'] = 'OSL-3.0';
            }
        }

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
