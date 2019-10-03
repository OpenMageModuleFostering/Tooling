<?php


namespace OpenMageModuleFostering\Tooling\Job;


use OpenMageModuleFostering\Tooling\RepositoryInfo;
use OpenMageModuleFostering\Tooling\Task\AbstractTask;
use OpenMageModuleFostering\Tooling\Task\ComposerJsonKeywordsUpdate;
use OpenMageModuleFostering\Tooling\Task\ComposerJsonMultilineFix;
use OpenMageModuleFostering\Tooling\Task\GitCommit;
use OpenMageModuleFostering\Tooling\Task\ReadmeUpdate;

class UpdateProjectInfo extends AbstractJob
{

    public function execute(RepositoryInfo $repositoryInfo)
    {

        $tasks = [];
        $tasks[] = new ComposerJsonMultilineFix(
            $repositoryInfo,
            $this->config
        );
        $tasks[] = new ComposerJsonKeywordsUpdate(
            $repositoryInfo,
            $this->config
        );
        $tasks[] = new ReadmeUpdate(
            $repositoryInfo,
            $this->config
        );

        $gitCommitTask = new GitCommit(
            $repositoryInfo,
            $this->config
        );
        $gitCommitTask->addFile('README.md');
        $gitCommitTask->addFile('composer.json');
        $gitCommitTask->setMessage("Update Readme and add keyword to composer json");
        $tasks[] = $gitCommitTask;
        /** @var AbstractTask $task */
        foreach ($tasks as $task) {
            $task->execute();
        }

    }
}
