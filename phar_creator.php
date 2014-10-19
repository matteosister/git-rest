<?php

require 'vendor/autoload.php';

use Herrera\Box\Box;
use Herrera\Box\StubGenerator;
use Symfony\Component\Finder\Finder;

@unlink('git-rest.phar');

$box = Box::create('git-rest.phar');
$phar = $box->getPhar();

$phar->buildFromDirectory(__DIR__, '/(?!phar_creator)/');