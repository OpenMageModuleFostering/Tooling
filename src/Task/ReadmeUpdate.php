<?php


namespace OpenMageModuleFostering\Tooling\Task;


class ReadmeUpdate extends AbstractTask
{

    protected function getReadmeContent()
    {
        $composerJson = file_get_contents($this->repositoryInfo->getLocalAbsolutePath('composer.json'));
        $composerJsonObject = json_decode($composerJson, true);
        $description = $composerJsonObject['description'];
        $content = <<<MARKDOWN
## Original Package Description

$description


## Disclaimer

This is a Repository imported from the Magento Marketplace, formerly known as Magento connect.
The purpose is, to archive them in a permanent way, you can read more about it in our blogbost: https://openmage.github.io/2019/08/18/new-home-magento-connect-modules.html
There is a chance, this repository misses some of the newer version.
We rely on our users to inform us about newer Releases. And if they can provide them to us, this would be even better.

### The original source is already on a public Git?

Please also inform us about them, then we can mark our repository as discontinued and refer to the other Repository.

### Contact us

the easiest way is to create an Issue here, we have an eye on them.

Alternative you can try to contact @Flyingmana directly.
 

MARKDOWN;

        return $content;
    }

    public function execute()
    {
        file_put_contents(
            $this->repositoryInfo->getLocalAbsolutePath('README.md'),
            $this->getReadmeContent()
        );
    }
}
