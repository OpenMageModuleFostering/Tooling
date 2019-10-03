<?php


namespace OpenMageModuleFostering\Tooling;

use OpenMageModuleFostering\Tooling;

class RepositoryProvider
{

    const PROJECTLIST_ALL = "PROJECTLIST_ALL";
    const PROJECTLIST_TEST = "PROJECTLIST_TEST";
    const PROJECTLIST_CONNECT = "PROJECTLIST_CONNECT";
    const PROJECTLIST_ARCHIVED = "PROJECTLIST_ARCHIVED";

    const AVAILABLE_PROJECTLISTS = [
        self::PROJECTLIST_ALL,
        self::PROJECTLIST_TEST,
        self::PROJECTLIST_CONNECT,
    ];

    protected $currentProjectList;

    protected $projectsCache = [];

    public function __construct($projectlist)
    {
        $this->currentProjectList = $projectlist;
        switch ($projectlist) {
            case self::PROJECTLIST_ALL:
            case self::PROJECTLIST_CONNECT:
                $projectNameList = json_decode(
                    file_get_contents(__DIR__ . '/../var/packageNames.json'),
                    true
                );
                $projectNameList = array_map(
                    function ($name) {
                        return str_replace("connect20/", "", $name);
                    },
                    $projectNameList
                );
                $projectNameList = $this->filterSkippedRepositories($projectNameList);
                $this->projectsCache = $projectNameList;
                break;
            case self::PROJECTLIST_TEST:
                $this->projectsCache = [
                    'zsoltnet_ledgerinvoice',
                    'zookal_mock',
                ];
                break;
        }
    }


    protected function filterSkippedRepositories($projectList)
    {
        $projectList = array_filter(
            $projectList,
            function ($name) {
                return !in_array($name,
                    [
                        '17805632160283535599',
                    ]
                );
            });
        return $projectList;
    }


    public function getCount()
    {
        return count($this->projectsCache);
    }

    public function getRepositoryObjectByIndex($index)
    {
        if (isset($this->projectsCache[$index])) {
            $name = $this->projectsCache[$index];
            $result = new RepositoryInfo(
                $name,
                Tooling\getConfig('var_dir') . 'git_modules/' . $name
            );
            return $result;
        }
    }

}
