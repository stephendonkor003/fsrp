<?php

use Illuminate\Contracts\Console\Kernel;
use PHPUnit\TextUI\Configuration\Builder as PHPUnitConfigurationBuilder;

require_once __DIR__ . '/../../vendor/autoload.php';

if (class_exists(PHPUnitConfigurationBuilder::class)) {
    $configurationFile = realpath(__DIR__ . '/../../phpunit.xml.dist') ?: __DIR__ . '/../../phpunit.xml.dist';

    (new PHPUnitConfigurationBuilder)->build([
        'phpunit',
        '--configuration',
        $configurationFile,
    ]);
}

$app = require __DIR__ . '/../../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

return $app;
