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
    if (strpos($packageName, 'connect20/conversify') === 0) {
        // tar of version 1.1.3 contains an absolute path
        // is propritary anyway
        echo "S";
        continue;
    }
    if (strpos($packageName, 'connect20/estdevs_newsletterpopup') === 0) {
        // tar of version 1.0.0 contains an <ul>
        echo "S";
        continue;
    }
    if (
        strpos($packageName, 'connect20/letssyncrollc_oct8ne') === 0
        ||  strpos($packageName, 'connect20/nuber_scopeviewer') === 0
        ||  strpos($packageName, 'connect20/shweta_newletter') === 0
        ||  strpos($packageName, 'connect20/vsourz_digital_jassor_slider') === 0
    ) {
        // tar of version 1.1.7: failed: Cannot extract ".", internal error
        // tar of version 0.1.1: failed: Cannot extract "", internal error  (path: "/")
        // tar of version 1.0.3: failed: Cannot extract "", internal error  (path: "../Newsletter/../Newsletter/..")
        // tar of version 1.0.1: failed:  filename "media/layerslider/banner1_thumb_1-slide_captionimg1-img-2015-04-10-08-43-43-slide_captionimg1-img-2015-04-28-05-51-00.png" is too long for tar file format
        echo "S";
        continue;
    }
    if (strpos($packageName, 'connect20/eflyermaker') === 0) {
        // tar of version 1.0.0 contains an directory name with a trailing space
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

foreach ($sortedPackagesByNameAndVersion as $packageName=> $deep1) {
    $packageRealName = explode('/',$packageName)[1];
    $gitDirectory = Tooling\getConfig('var_dir').'git_modules/'.$packageRealName;
    if(file_exists($gitDirectory)) {
        echo "Skip Package: $packageName \n";
        continue;
    }
    Tooling\initializeGitRepository($gitDirectory);
    foreach ($deep1 as $versionMajor=> $deep2) {
        foreach ($deep2 as $versionMinor => $deep3) {
            foreach ($deep3 as $versionBugfix => $packageDefinition) {
                Tooling\addVersionToGitRepository($gitDirectory, $packageDefinition);
                // break 4; //stop after first Version
            }
        }
    }
}

echo PHP_EOL;
echo "memory peak usage: " . (memory_get_peak_usage(true)/1024/1024) . "MB\n";


