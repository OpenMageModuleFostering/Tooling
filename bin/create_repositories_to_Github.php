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

    if (($counter % 50) == 0) {
        /** @var \Github\Api\RateLimit\RateLimitResource $rateLimits */
        $rateLimits = $client->api('rate_limit')->getResource('core');
        $secondsTillReset = $rateLimits->getReset()-time();
        echo " ($counter / $numberOfPackages) rateLimit({$rateLimits->getLimit()} / {$rateLimits->getRemaining()} / {$secondsTillReset}s )\n";
    }
    $counter++;
    if ($counter<2000) {
        echo ".";
        continue;
    }
    $doesExistAlready = Tooling\githubRepositoryExists(
        $repo,
        $packageName,
        $vendorName
    );
    if ($doesExistAlready) {
        echo "S";
        continue;
    }
    Tooling\githubRepositoryCreate(
        $repo,
        $packageName,
        $vendorName
    );

    echo "C";
}




return;
$query =<<<'GRAPHQL'
query {
  repository(owner:"$vendorName") {
    issues(last:20, states:CLOSED) {
      edges {
        node {
          title
          url
          labels(first:5) {
            edges {
              node {
                name
              }
            }
          }
        }
      }
    }
  }
}
GRAPHQL;


$graphQl->execute();


return;

$existingRepositories = $repo->org(
    $vendorName,
    [
        'sort' => 'full_name',
    ]
);

var_dump($existingRepositories);


