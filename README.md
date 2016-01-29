# custom-twig-extension

## Introduction

A simple and small dependency manager for javascript and CSS files. All files will be combined, minified and cached. Less and Scss files are also supported.

## Requirements

    - matthiasmullie/minify ^1.3
    - doctrine/cache ^1.6
    - leafo/scssphp ^0.6.3
    - tedivm/jshrink ^1.1
    - leafo/lessphp ^0.5.0
    - twig/twig ^1.23

## Installation

1. Install with composer
```composer require crecket/dependency-manager```

2. Add the twig extension to your twig view:

```
$twig->addExtension(new Crecket\DependencyManager\Response());
```

## Usage

Add a file to the manager

```
use Crecket\DependencyManager\Loader;

Loader::addJsFile('testFile.js');
Loader::addCssFile('testFile.css');
```

In your twig template

```
<script src="/minify.php?files={{ getJsList(true) }}">
```

Or if you want the minified version, leave the parameter empty

```
<link href="/minify.php?files={{ getCssList() }}" rel="stylesheet">
```

Now create a new file named minify.php for example and add the following line.

```
$options = array(
    'Cache' => '/cache' // Location based on the root

);
$Response = new Crecket\DependencyManager\Response($options);
```

If you want to make this more secure and ensure only you can create a file list add a secret key like this:

```
Loader::Secret('some_secret');
```

And add the same secret in the Response options.

```
$options = array(
    'Secret' => 'some_secret' // A secret key to create a security hash, OPTIONAL
);
$Response = new Crecket\DependencyManager\Response($options);
```