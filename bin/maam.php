#!/usr/bin/env php
<?php

use Moose\Maam\Generator\Generator;

require $argv[1];

$classMap = [];
$generator = new Generator($argv[2], $argv[3]);
$classMap = $generator->generate();

echo "Rebuilt classmap:\n";
var_dump($classMap);
