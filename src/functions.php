<?php

namespace OpenMageModuleFostering\Tooling;


use Github\Exception\RuntimeException;

/**
 * @param string $path
 * @return mixed
 */
function getConfig($path) {
    $config = require __DIR__ . '/../config.php';
    return $config[$path];
}

function fetch_file_with_cache($url, $throttle = 0) {
    $escapedUrl = preg_replace('/[^A-Za-z0-9_\-]/', '_', $url);
    $parsedUrl = parse_url($url);
    $escapedDomain = preg_replace('/[^A-Za-z0-9_\-]/', '_', $parsedUrl['host']);

    $cachedPath = getConfig('cache_dir') . 'file_fetcher/' . $escapedDomain . '/' . $escapedUrl;

    if (file_exists($cachedPath)) {
        return file_get_contents($cachedPath);
    }

    if ($throttle > 0) {
        sleep($throttle);
    }
    $content = file_get_contents($url);
    if (!file_exists(getConfig('cache_dir') . 'file_fetcher/' . $escapedDomain)) {
        mkdir(getConfig('cache_dir') . 'file_fetcher/' . $escapedDomain,0777,true);
    }
    file_put_contents($cachedPath, $content);

    return $content;

}


function arrayDeepSet(&$array, $value, ...$keys) {
    if (isset($keys[4])) {
        if (!isset($array[$keys[0]])) {
            $array[$keys[0]] = [];
        }
        if (!isset($array[$keys[0]][$keys[1]])) {
            $array[$keys[0]][$keys[1]] = [];
        }
        if (!isset($array[$keys[0]][$keys[1]][$keys[2]])) {
            $array[$keys[0]][$keys[1]][$keys[2]] = [];
        }
        if (!isset($array[$keys[0]][$keys[1]][$keys[2]][$keys[3]])) {
            $array[$keys[0]][$keys[1]][$keys[2]][$keys[3]] = [];
        }
        $array[$keys[0]][$keys[1]][$keys[2]][$keys[3]][$keys[4]] = $value;
        return;
    }
    if (isset($keys[3])) {
        if (!isset($array[$keys[0]])) {
            $array[$keys[0]] = [];
        }
        if (!isset($array[$keys[0]][$keys[1]])) {
            $array[$keys[0]][$keys[1]] = [];
        }
        if (!isset($array[$keys[0]][$keys[1]][$keys[2]])) {
            $array[$keys[0]][$keys[1]][$keys[2]] = [];
        }
        $array[$keys[0]][$keys[1]][$keys[2]][$keys[3]] = $value;
        return;
    }
    if (isset($keys[2])) {
        if (!isset($array[$keys[0]])) {
            $array[$keys[0]] = [];
        }
        if (!isset($array[$keys[0]][$keys[1]])) {
            $array[$keys[0]][$keys[1]] = [];
        }
        $array[$keys[0]][$keys[1]][$keys[2]] = $value;
        return;
    }
    if (isset($keys[1])) {
        if (!isset($array[$keys[0]])) {
            $array[$keys[0]] = [];
        }
        $array[$keys[0]][$keys[1]] = $value;
        return;
    }
    if (isset($keys[0])) {
        $array[$keys[0]] = $value;
    }
}

function passtruh_wrapper($command)
{
    echo "executing: ". $command . PHP_EOL;
    passthru($command);
}

function initializeGitRepository($directory)
{
    mkdir($directory, 0777, true);
    chdir($directory);
    passtruh_wrapper('git init');
    file_put_contents('.gitignore', '');
}

function generateComposerJsonContent($packageDefinition)
{
    if(!isset($packageDefinition['license'])){
        $packageDefinition['license'] = [];
    }
    if (count($packageDefinition['license']) < 1) {
        $packageLicense = '';
        file_put_contents(
            getConfig('var_dir').'license_without.log',
            $packageDefinition['name'].PHP_EOL,
            FILE_APPEND
        );
    }
    if (count($packageDefinition['license']) > 1) {
        var_dump($packageDefinition);
        throw new \Exception("license issue");
    }
    if (count($packageDefinition['license']) == 1) {
        $packageLicense = $packageDefinition['license'][0];
    }
    $packageRealName = explode('/',$packageDefinition['name'])[1];
    $vendorName = getConfig('new_vendor');
    $authorsJson = json_encode($packageDefinition['authors'], JSON_PRETTY_PRINT);
    $content = <<<JSON
{
    "name": "$vendorName/$packageRealName",
    "type": "magento-module",
    "license": "{$packageLicense}",
    "homepage": "{$packageDefinition['homepage']}",
    "description": "{$packageDefinition['description']}",
    "authors": {$authorsJson},
    "suggest": {
        "magento-hackathon/magento-composer-installer": "*"
    }
}
JSON;

    return $content;

}

function addVersionToGitRepository($directory, $packageDefinition )
{
    $timestamp = microtime();
    $timestamp = str_replace(' ', '_', $timestamp);
    $timestamp = str_replace('.', '_', $timestamp);
    chdir($directory);
    passtruh_wrapper('git add .');
    passtruh_wrapper('git rm -r .');
    $content = fetch_file_with_cache($packageDefinition['dist']['url']);
    $tempModulePath = getConfig('cache_dir')."/temp{$timestamp}_module.tgz";
    $tempModuleTarPath = getConfig('cache_dir')."/temp{$timestamp}_module.tar";
    file_put_contents($tempModulePath, $content);
    echo "extracting package: {$packageDefinition['name']} in version {$packageDefinition['version']} \n";
    $phar = new \PharData($tempModulePath, \FilesystemIterator::SKIP_DOTS);
    $phar->decompress();
    $phar->extractTo($directory, null, true);
    unlink($tempModulePath);
    unlink($tempModuleTarPath);
    file_put_contents('composer.json', generateComposerJsonContent($packageDefinition));
    passtruh_wrapper('git add .');
    passtruh_wrapper('git commit -m "import connect version '.$packageDefinition['version'].' " --author="OpenMage Import Bot <flyingmana+openmage_bot@googlemail.com>"');
    passtruh_wrapper('git tag -a '.$packageDefinition['version'].' -m "import connect version '.$packageDefinition['version'].' "');

}

function githubRepositoryExists(
    \Github\Api\Repo $apiRepo,
    $repositoryName,
    $organizationName
) {
    try {
        $repositoryInfo = $apiRepo->show($organizationName, $repositoryName);
    } catch (RuntimeException $exception) {
        // throws exception if repository does not exist
        return false;
    }
    return true;
}

function githubRepositoryCreate(
    \Github\Api\Repo $apiRepo,
    $repositoryName,
    $organizationName
) {
    $apiRepo->create(
        $repositoryName,
        $description = '',
        $homepage = '',
        $public = true,
        $organization = $organizationName,
        $hasIssues = true,
        $hasWiki = false,
        $hasDownloads = true,
        $teamId = null,
        $autoInit = false,
        $hasProjects = true
    );
}


