# dependency-manager

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
```composer require crecket/dependency-manager ```

2. Add the twig extension to your twig view:

```$twig->addExtension(new Crecket\DependencyManager\Response());```

## Usage

Add a file to the manager

```
use Crecket\DependencyManager\Loader;

Loader::addJsFile('testFile.js');
Loader::addCssFile('testFile.css');
```

In your twig template create a script source. The first parameter is optional and enables/disables minifying your code.

```<script src="/minify.php{{ getJsList(true) }}">```

Or if you aren't using twig

```<link href="/minify.php<?php echo Crecket\DependencyManager\Loader::GetCssLink(true); ?>" rel="stylesheet">```

Now create a new file named minify.php for example and add the following line.

```
$options = array(
    'Cache' => '/cache' // Location based on the root
);

define('ROOT', __DIR__); // Don't forget this! 

try{
    echo $Response = new Crecket\DependencyManager\Response($options);
}catch(Crecket\DependencyManager\Exception $ex){ // catch errors
    echo $ex->getTitle();
    echo '<br>';
    echo $ex->getMessage();
}
```

## Debugging

Response takes a optional second parameter which will make sure the session storage and secret key are ignored.

```
$file_list = array('/some/js/file.js', '/another/js/files.js');
echo $Response = new Crecket\DependencyManager\Response($options, $file_list);
```

#### Security

To ensure only you can create a file list add a secret key. Make sure this key is secure/long enough!

```
Loader::Secret('some_secret');
```

