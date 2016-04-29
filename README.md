# dependency-manager

[![Build Status](https://travis-ci.org/Crecket/dependency-manager.svg?branch=master)](https://travis-ci.org/Crecket/dependency-manager) [![Latest Release](https://img.shields.io/github/release/crecket/Dependency-manager.svg)](https://github.com/Crecket/dependency-manager)

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


### Adding new files

##### addFiles($fileList, $groupName);

- PARAM1:  enter a single string to a file location or a array with multiple files.
- PARAM2:  the group name, this way you can store multiple file groups


    use Crecket\DependencyManager\Loader;
    Loader::addFiles('testFile.css', 'cssGroupName');
    
    // different group and multiple files this time
    Loader::addFiles(array('testFile.css', '/some/folder/file_name.css'), 'cssGroupName2'); 
    
### Remove files
    
##### removeFiles($groupName);

- PARAM1: contains the file group name.


    Loader::removeFiles('cssGroupName2');

### Create a url

##### Twig: getFilesLink(minify, groupName)

- PARAM1: Minify the files or not
- PARAM2: Group name

`<script src="/minify.php{{ getFilesLink(true, 'jsGroupName') }}">`

##### PHP: getFilesLink(

- PARAM1: Minify the files or not
- PARAM2: Group name

`<link href="/minify.php<?php echo Crecket\DependencyManager\Loader::getFilesLink(true, 'cssGroupName'); ?>" rel="stylesheet">`


### Response 

##### Standard php example

    $options = array(
        // Location that the default Doctrine/FilesystemCache will use. Location is based on the root
        // Required if no custom cache object is given
        'CacheLocation' => '/cache',

        // Optional, namespace to use for the doctrine file system cache
        'CacheNamespace'  => 'DependencyManagerNamespace',
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

##### Silex route example

You can quite easily use the url generator in silex to create a url to this route using

    <script src="{{ url('dependency_minify', {id: GetFilesHash(false, 'jsGroupExample')}) }}"></script>

or without twig

    <script src="/minify/<?php echo Loader::getHash(true, 'jsGroupExample'); ?>"

Next create a route: 

    // ID contains the hash id
    $app->get('/minify/{id}', function ($id) use ($app) {
    
        // Options
        $options = array(
            'CacheLocation' => '/cache',
            'CacheNamespace' => 'DependencyManagerNamespace'
        );
    
        try {
            // second parameter contains the hash id
            $Response = new Crecket\DependencyManager\Response($options, $id);
    
            // Get the response data
            $response_data = $Response->getResult();
    
            // Get the headers and status code for this request
            $header_data = Crecket\DependencyManager\Utilities::getHeaders();
    
        } catch (Crecket\DependencyManager\Exception $ex) { // catch errors
            // return a error
            return new Symfony\Component\HttpFoundation\Response(
                $ex->getTitle() . '<br>' . $ex->getMessage(),
                500,
                array()
            );
        }
        
        // Return content, headers and status code
        return new Symfony\Component\HttpFoundation\Response(
            $response_data,
            $header_data['status_code'],
            $header_data['headers']
        );
    })->bind('dependency_minify');


## Debugging

Response takes a optional second parameter which will make sure the session storage and secret key are ignored. This can be a hash id to retrieve the files stored in the session, or a direct array with files

    $file_list = array('/some/js/file.js', '/another/js/files.js');
    $Response = new Crecket\DependencyManager\Response($options, $file_list);
    
    // OR
    
    $file_hash = Loader::getHash($minify, $groupName);
    $Response = new Crecket\DependencyManager\Response($options, $file_hash);
    


## Security

To ensure only you can create a file list add a secret key. Make sure this key is secure/long enough!

`Loader::Secret('some long secret passphrase');`

## Issues

#### Font files in Css
External font files, for example bootstrap's Glyphicons my return 404 errors because the path has changed. These can usually be fixed by copying the font files and moving them to a `/fonts` folder in your web root.
