<?php

use Supercluster\Gravity\Configuration\BootableContainer;

/**
 * Supercluster Web Application
 *
 * Outputs the application to the web
 *
 * @see supercluster.ini
 */

// Steps down one dir
chdir(__DIR__ . '/..');

// Requires the Composer autoloader
require 'vendor/autoload.php';

try {

    // Try to run the contained application
    $container = new BootableContainer('supercluster.package.ini');
    print $container->run();

} catch (Exception $e) {

    // Wraps uncaught exceptions to a generic error
    header('HTTP/1.1 500 Server Error');
    echo "Unexpected Error.";
    throw $e;

}
