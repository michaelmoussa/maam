#!/usr/bin/php
<?php

use Moose\Maam\Generator\Generator;

include __DIR__ . '/../vendor/autoload.php';

if (!isset($argv[1])) {
    die("Missing source path!\n");
} elseif (!is_dir($argv[1])) {
    die("The provided source path was not found\n");
}

$classMap = [];
$generator = new Generator();
$classMap = $generator->generate($argv[1]);

echo "Rebuilt classmap:\n";
var_dump($classMap);
