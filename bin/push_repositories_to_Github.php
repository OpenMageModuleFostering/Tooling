<?php
use OpenMageModuleFostering\Tooling;

require __DIR__ . '/../bootstrap.php';

$vendorName = Tooling\getConfig('new_vendor');


$client = new \Github\Client(null, 'v3');

$client->authenticate(Tooling\getConfig('github_token'), null, \Github\Client::AUTH_HTTP_TOKEN);

/** @var \Github\Api\Repo $repo */
$repo = $client->api('repo');

$graphQl = $client->graphql();


$packageNames = json_decode(file_get_contents(Tooling\getConfig('var_dir').'packageNames.json'), true);
$numberOfPackages = count($packageNames);
echo "Number of Packages: " . $numberOfPackages . "\n";
$counter = 0;
foreach ($packageNames as $packageName) {
    $packageName = str_replace('connect20/', '', $packageName);
    $gitDirectory = Tooling\getConfig('var_dir').'git_modules/'.$packageName;
    $gitRemoteUrl = "git@github.com:OpenMageModuleFostering/$packageName.git";

    if (($counter % 50) == 0) {
        /** @var \Github\Api\RateLimit\RateLimitResource $rateLimits */
        $rateLimits = $client->api('rate_limit')->getResource('core');
        $secondsTillReset = $rateLimits->getReset()-time();
        echo " ($counter / $numberOfPackages) rateLimit({$rateLimits->getLimit()} / {$rateLimits->getRemaining()} / {$secondsTillReset}s )\n";
    }
    $counter++;
    if ($counter<0) {
        echo ".";
        continue;
    }
    Tooling\githubAddOrigin(
        $gitDirectory,
        $gitRemoteUrl
    );
    Tooling\githubPushRepository(
        $gitDirectory,
        $gitRemoteUrl
    );

    echo "P";
}

