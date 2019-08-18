<?php

$localConfig = null;
if( file_exists(__DIR__ . '/config.local.php')) {
    $localConfig = include __DIR__ . '/config.local.php';
}


return [

    'packages_source' => 'https://packages.firegento.com/',

    'new_vendor' => 'OpenMageModuleFostering',
    'githubOrg' => 'OpenMageModuleFostering',

    'var_dir' => __DIR__ . '/var/',
    'cache_dir' => __DIR__ . '/var/tmp/',
    'tmp_modules_locations' => __DIR__ . '/var/modules/',

    'github_token' => ($localConfig && $localConfig['github_token']) ? $localConfig['github_token']:null,

];

