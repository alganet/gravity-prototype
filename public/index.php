<?php

use Supercluster\Gravity\BootableContainer;

/**
 * Supercluster Web Application
 *
 * Outputs the application to the web
 *
 * @see supercluster.ini
 * @see boot.php
 */

chdir(__DIR__ . '/..');

require 'vendor/autoload.php';

try {
    if (file_exists('supercluster.package.serial')) {
        $container = unserialize(file_get_contents('supercluster.package.serial'));
    } else {
        $container = new BootableContainer('supercluster.package.ini');
        file_put_contents('supercluster.package.serial', serialize($container));
    }
    print $container->run();
} catch (Exception $e) {
    header('HTTP/1.1 500 Server Error');
    echo "Unexpected Error.";
    throw $e;
}
