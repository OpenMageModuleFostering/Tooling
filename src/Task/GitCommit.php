<?php


namespace OpenMageModuleFostering\Tooling\Task;


use function OpenMageModuleFostering\Tooling\passtruh_wrapper;

/**
 * expect a password for signed commits, when activated via gitconfig
 *
 * Class GitCommit
 * @package OpenMageModuleFostering\Tooling\Task
 */
class GitCommit extends AbstractTask
{

    protected $message = "commit changes";

    protected $pathsToAdd = [];

    public function addFile($path)
    {
        $this->pathsToAdd[] = $path;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function execute()
    {
        chdir($this->repositoryInfo->getLocalBaseDirectory());
        foreach ($this->pathsToAdd as $path) {
            passtruh_wrapper('git add ' . $path);
        }
        passtruh_wrapper('git commit -m "' . $this->message . '"'
            . ' --author="OpenMage Import Bot <flyingmana+openmage_bot@googlemail.com>"');


    }
}
