<?php
date_default_timezone_set('Europe/Amsterdam');
require 'vendor/autoload.php';

$options = array(
    'Cache' => '/cache',
    'Secret' => 'some_secret',
    'DirWhitelist' => array('/assets')
);

$Response = new Crecket\DependencyManager\Response($options);
