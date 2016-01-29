<?php
require 'vendor/autoload.php';

$options = array(
    'Cache' => '/cache',
    'Secret' => 'some_secret'
);

$Response = new Crecket\DependencyManager\Response($options);
