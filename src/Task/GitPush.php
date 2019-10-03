<?php


namespace OpenMageModuleFostering\Tooling\Task;


use function OpenMageModuleFostering\Tooling\passtruh_wrapper;

class GitPush extends AbstractTask
{

    public function execute()
    {
        chdir($this->repositoryInfo->getLocalBaseDirectory());
        passtruh_wrapper("git push -u origin master");
        sleep(2);


    }
}
