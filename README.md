# dependency-manager

[![Build Status](https://travis-ci.org/Crecket/dependency-manager.svg?branch=develop)](https://travis-ci.org/Crecket/dependency-manager) [![Latest Release](https://img.shields.io/github/release/crecket/Dependency-manager.svg)](https://github.com/Crecket/dependency-manager)

## Introduction

A simple and small dependency manager for javascript and CSS files. All files will be combined, minified and cached. Less and Scss files are also supported and all css output is run through a auto-prefixer.

## Requirements

    - matthiasmullie/minify ^1.3
    - doctrine/cache ^1.6
    - leafo/scssphp ^0.6.3
    - tedivm/jshrink ^1.1
    - leafo/lessphp ^0.5.0
    - twig/twig ^1.23
    - css-crush/css-crush ^dev-master

## Installation

1. Install with composer
    
    `composer require crecket/dependency-manager`

2. Add the twig extension to your twig view:

    `$twig->addExtension(new Crecket\DependencyManager\Response());`

## Usage

Add a file to the manager

    use Crecket\DependencyManager\Loader;

    Loader::addJsFile('testFile.js'); // string
    Loader::addJsFiles(array('testFile.js')); // array

    Loader::addCssFile('testFile.css'); // string
    Loader::addCssFile(array('testFile.css')); // array

Function accepts strings and array with file locations. Second parameter sets the group for this file

    Loader::addFiles('testFile.css', 'cssGroupName');
    Loader::addFiles(array('testFile.css'), 'cssGroupName2'); // different group

Remove all files for a group name

    Loader::removeFiles('cssGroupName2');


In your twig template create a script source. The first parameter is optional and enables/disables minifying your code.

    <script src="/minify.php{{ getJsList(true) }}">
    <link href="/minify.php<?php echo Crecket\DependencyManager\Loader::getCssLink(true); ?>" rel="stylesheet">

Or if you are using groups, use the ` getFileList($minify, $group_name)`  function

    <script src="/minify.php{{ getFilesLink(true, 'jsGroupName') }}">
    <link href="/minify.php<?php echo Crecket\DependencyManager\Loader::getFilesLink(true, 'cssGroupName'); ?>" rel="stylesheet">


Youc can also load only the files for a specific group


Now create a new file named minify.php for example and add the following line.

    $options = array(
        // Location that the default Doctrine/FilesystemCache will use. Location is based on the root
        // Required if no custom cache object is given
        'Cache' => '/cache',

        // Optional, namespace to use for the doctrine file system cache
        'CacheNamespace'  => 'DependencyManagerNamespace',

        // OR write your own cache interface, make sure it implements the Crecket\DependencyManager\CacheAdapaterInterface
        'CacheObject' => new CustomCacheInterface()
    );
    
    define('ROOT', __DIR__); // Don't forget this! 
    
    try{
        $Response = new Crecket\DependencyManager\Response($options);
        
        echo $Response->getResult();
    }catch(Crecket\DependencyManager\Exception $ex){ // catch errors
        echo $ex->getTitle();
        echo '<br>';
        echo $ex->getMessage();
    }

## Debugging

Response takes a optional second parameter which will make sure the session storage and secret key are ignored.

    $file_list = array('/some/js/file.js', '/another/js/files.js');
    $Response = new Crecket\DependencyManager\Response($options, $file_list);


## Security

To ensure only you can create a file list add a secret key. Make sure this key is secure/long enough!

`Loader::Secret('some long secret passphrase');`

## Issues

#### Font files in Css
External font files, for example bootstrap's Glyphicons my return 404 errors because the path has changed. These can usually be fixed by copying the font files and moving them to a `/fonts` folder in your web root.
