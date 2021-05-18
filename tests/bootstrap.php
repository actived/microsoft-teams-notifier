<?php

if (!is_readable(__DIR__ . '/../vendor/autoload.php')) {
    die(PHP_EOL . "Missing Vendor Dependencies. Please run 'composer install'." . PHP_EOL);
}
require_once __DIR__ . '/../vendor/autoload.php';