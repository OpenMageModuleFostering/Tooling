<?php


namespace OpenMageModuleFostering\Tooling\Job;


use OpenMageModuleFostering\Tooling\RepositoryInfo;
use OpenMageModuleFostering\Tooling\Task\AbstractTask;
use OpenMageModuleFostering\Tooling\Task\GitPush;

class UpdateProjectInfo extends AbstractJob
{

    public function execute(RepositoryInfo $repositoryInfo)
    {

        $tasks = [];
        $tasks[] = new GitPush(
            $repositoryInfo,
            $this->config
        );
        /** @var AbstractTask $task */
        foreach ($tasks as $task) {
            $task->execute();
        }

    }
}
