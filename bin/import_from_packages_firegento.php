<?php

use OpenMageModuleFostering\Tooling;

require __DIR__ . '/../src/functions.php';
$config = require __DIR__ . '/../config.php';


$packagesFilePrimary = file_get_contents($config['packages_source'].'/packages.json');
$packagesPrimary = json_decode($packagesFilePrimary, true);

if (count($packagesPrimary['includes']) !== 1) {
    throw new Exception('invalid includes amount in file:"' . $packagesFilePrimary . '"');
}
foreach ($packagesPrimary['includes'] as $key => $value) {
    $packagesFile = Tooling\fetch_file_with_cache(
        $config['packages_source']
        . '/'
        . $key
        , 2
    );
}

// download and cache all modules


$packageNames = [];
$packageRepo = json_decode($packagesFile, true)['packages'];
$numberOfPackages = count($packageRepo);
$sortedPackagesByNameAndVersion = [];
echo "Number of Packages: " . $numberOfPackages . "\n";
$counter = 0;
foreach ($packageRepo as $packageName => $packages) {
    $counter++;
    if (($counter % 50) == 0) {
        echo " ($counter / $numberOfPackages)\n";
    }
    if (strpos($packageName, 'connect20/yotporeviews') === 0) {
        echo "S";
        continue;
    }
    if (strpos($packageName, 'connect20/') !== 0) {
        echo "S";
        continue;
    }
    $packageNames[] = $packageName;
    foreach ($packages as $version => $packageDefinition) {
        $packageVersion = explode(".", $version);
        if (!isset($packageVersion[1])) {
            $packageVersion[1] = 0;
        }
        if (!isset($packageVersion[2])) {
            $packageVersion[2] = 0;
        }
        Tooling\arrayDeepSet(
            $sortedPackagesByNameAndVersion,
            $packageDefinition,
            $packageName,
            $packageVersion[0],
            $packageVersion[1],
            $packageVersion[2]
        );
        Tooling\fetch_file_with_cache($packageDefinition['dist']['url']);
    }
    echo "D";
}


file_put_contents(Tooling\getConfig('var_dir').'packageNames.json', json_encode($packageNames, JSON_PRETTY_PRINT));

var_dump($sortedPackagesByNameAndVersion);

echo PHP_EOL;
echo "memory peak usage: " . (memory_get_peak_usage(true)/1024/1024) . "MB\n";


