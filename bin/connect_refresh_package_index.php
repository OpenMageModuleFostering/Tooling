<?php

use OpenMageModuleFostering\Tooling;

require __DIR__ . '/../src/functions.php';
$config = require __DIR__ . '/../config.php';

if (false) {
    $connectPackagesXmlContent = file_get_contents('https://connect20.magentocommerce.com/community/packages.xml');
    file_put_contents(
        Tooling\getConfig('var_dir').'connectPackages.xml',
        $connectPackagesXmlContent
    );
}
$connectPackagesXmlContent = file_get_contents(Tooling\getConfig('var_dir').'connectPackages.xml');

$doc = new DOMDocument();
$doc->loadXML($connectPackagesXmlContent);
$connectPackageIndex = [];
foreach ($doc->getElementsByTagName('p') as $package) {
    /** @var DOMElement $package */
    //var_dump($doc->saveXML($package));
    $packageValues = [
        'name' => $package->getElementsByTagName('n')[0]->nodeValue,
        'version_stable' => $package->getElementsByTagName('s')[0]->nodeValue,
    ];
    //var_dump($packageValues);
    $connectPackageIndex[] = $packageValues;
}

file_put_contents(Tooling\getConfig('var_dir').'connectPackageIndex.json', json_encode($connectPackageIndex, JSON_PRETTY_PRINT));
