<?php

use OpenMageModuleFostering\Tooling;

require __DIR__ . '/../bootstrap.php';

$packageIndex = json_decode(file_get_contents(Tooling\getConfig('var_dir').'connectPackageIndex.json'), true);


$numberOfPackages = count($packageIndex);
echo "Number of Packages: " . $numberOfPackages . "\n";
$counter = 0;

$packageVersionIndex = [];
foreach ($packageIndex as $package) {
    $counter++;
    if (($counter % 50) == 0) {
        echo " ($counter / $numberOfPackages)\n";
    }
    $connectPackagesXmlContent = file_get_contents(
        'https://connect20.magentocommerce.com/community/'.$package['name'].'/releases.xml'
    );
    $doc = new DOMDocument();
    $doc->loadXML($connectPackagesXmlContent);
    $packageVersions = [];
    foreach ($doc->getElementsByTagName('r') as $versionElement) {
        if($versionElement->getElementsByTagName('s')[0]->nodeValue !== 'stable') {
            continue;
        }
        $version = $versionElement->getElementsByTagName('v')[0]->nodeValue;
        $downloadUrl = 'https://connect20.magentocommerce.com/community/'.$package['name'].'/'.$version.'/'.$package['name'].'-'.$version.'.tgz';

        $packageFile = @Tooling\fetch_file_with_cache(
            'https://connect20.magentocommerce.com/community/'.$package['name'].'/'.$version.'/'.$package['name'].'-'.$version.'.tgz'
            , 2
        );
        // https://connect20.magentocommerce.com/community/super-cache-hint/1.0.0.1/super-cache-hint-1.0.0.1.tgz
//        $connectPackageVersionXmlContent = file_get_contents(
//            'https://connect20.magentocommerce.com/community/'.$package['name'].'/'.$version.'/package.xml'
//        );
//        echo $connectPackageVersionXmlContent;
//        return;
        $packageVersions[] = [
            'version' => $version,
            'dist_url' => $downloadUrl,
            'has_error' => false === $packageFile || "" === $packageFile,
        ];
    }
    if (!empty($packageVersions)) {
        $packageVersionIndex[$package['name']] = $packageVersions;
        echo "D";
    } else {
        echo "E";
    }
//    echo $connectPackagesXmlContent;
//    return;
}

file_put_contents(
    Tooling\getConfig('var_dir').'connectPackageVersionIndex.json',
    json_encode($packageVersionIndex, JSON_PRETTY_PRINT)
);


