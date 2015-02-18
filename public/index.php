<?php

use Supercluster\Gravity\Boot;

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
    (new Boot('supercluster.package.ini'))->run();
} catch (Exception $e) {
    header('HTTP/1.1 500 Server Error');
    echo "Unexpected Error.";
    throw $e;
}