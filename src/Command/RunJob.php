<?php


namespace OpenMageModuleFostering\Tooling\Command;


use OpenMageModuleFostering\Tooling\Job\AbstractJob;
use OpenMageModuleFostering\Tooling\RepositoryProvider;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunJob extends Command
{
    protected static $defaultName = 'job:run';

    protected function configure()
    {
        $this->addArgument(
            'job',
            InputArgument::REQUIRED,
            'The Job to run'
        );
        $this->addArgument(
            'projectList',
            InputArgument::OPTIONAL,
            'one of the project Lists to run the job against (default: PROJECTLIST_TEST)'
        );
        $this->addArgument(
            'startIndex',
            InputArgument::OPTIONAL,
            'default: 0'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $jobName = $input->getArgument('job');
        $projectListArgument = $input->getArgument('projectList');
        if (!$projectListArgument) {
            $projectListArgument = RepositoryProvider::PROJECTLIST_TEST;
        }

        $repositoryProvider = new RepositoryProvider($projectListArgument);
        $numberOfRepositories = $repositoryProvider->getCount();

        $startIndex = $input->getArgument('startIndex');
        if (!$startIndex) {
            $startIndex = 0;
        }

        $jobClassName = '\\OpenMageModuleFostering\\Tooling\\Job\\' . $jobName;
        if (
            !class_exists($jobClassName)
            || !is_subclass_of($jobClassName, AbstractJob::class)
        ) {
            $output->writeln(
                "<error>Job with Name \"$jobName\" not found</error>"
            );
            return;
        }

        /** @var AbstractJob $jobObject */
        $jobObject = new $jobClassName;

        $output->writeln(
            "Start Job Run for ProjectList \"$projectListArgument\" containing \"$numberOfRepositories\" Repositories, starting at Index \"$startIndex\""
        );


        for ($i = $startIndex; $i <= $numberOfRepositories; $i++) {
            $repositoryInfo = $repositoryProvider->getRepositoryObjectByIndex($i);
            if (!$repositoryInfo) {
                continue;
            }
            $output->writeln('start Index (' . $i . ') Repository: ' . $repositoryInfo->getName());

            $jobObject->execute($repositoryInfo);

            $output->writeln('finish Index (' . $i . ') Repository: ' . $repositoryInfo->getName());
        }


        $output->writeln('Command successfully finished!');
    }
}
