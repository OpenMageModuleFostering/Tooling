<?php

use OpenMageModuleFostering\Tooling;

require __DIR__ . '/../bootstrap.php';


use Symfony\Component\Console\Application;

$application = new Application();

$application->add(new Tooling\Command\RunJob());

$application->run();
