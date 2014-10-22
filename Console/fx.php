<?php
use Floxim\Floxim\System\Fx as fx;

// current dir /vendor/floxim/floxim/console/
require_once(dirname(__DIR__) . '/../../../boot.php');

fx::log('olo', $_SERVER, $_ENV);

$manager = new \Floxim\Floxim\System\Console\Manager();
$manager->addCommands(fx::config('console.commands'));
$manager->addPath(__DIR__ . '/Command');
fx::log($manager);
$manager->run();