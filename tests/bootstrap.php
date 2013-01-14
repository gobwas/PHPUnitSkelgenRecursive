<?php

require_once dirname(__DIR__).'/vendor/autoload.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespace('RecursivePHPUnitSkelgen', dirname(__DIR__).'/src');
$loader->register();