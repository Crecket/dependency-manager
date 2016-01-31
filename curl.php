<?php

require 'vendor/autoload.php';

Crecket\DependencyManager\Loader::addJsFile('/assets/testfile1.js');
Crecket\DependencyManager\Loader::addJsFile('/assets/testfile2.js');
Crecket\DependencyManager\Loader::addCssFile('/assets/test_file1.scss');
Crecket\DependencyManager\Loader::addCssFile('/assets/test_file2.less');
Crecket\DependencyManager\Loader::addCssFile('/assets/test_file3.css');

Crecket\DependencyManager\Loader::Secret('some_secret');


$loader = new Twig_Loader_Array(array(
    'index.html' => '
<div class="chat-messages">
    JS url: <a href="/minify.php{{ getJsLink(true) }}">JS Link</a><br>
    CSS url: <a href="/minify.php{{ getCssLink(true) }}">CSS Link</a><br>
    <script src="/minify.php{{ getJsLink(true) }}"></script>
    </div>
    <link href="/minify.php{{ getCssLink(true) }}" type="text/css" rel="stylesheet">
    <!--<link href="/assets/test_file3.css" type="text/css" rel="stylesheet">-->
    ',
));
$twig = new Twig_Environment($loader);
$twig->addExtension(new Crecket\DependencyManager\TwigResponse());

echo $twig->render('index.html');
