# custom-twig-extension

## Introduction

A simple and small dependency manager for javascript and CSS files using [meenie/Munee](https://github.com/meenie/munee).

## Requirements

    - Munee ^1.7
    - Twig ^1.23

## Installation

1. Install with composer
```composer require crecket/dependency-manager```

2. Add the extension to the twig view:

```
$twig->addExtension(new Crecket\DependencyManager\Response());
```

## Usage

Add a file to the manager

```
Crecket\DependencyManager\Registry::addJsFile('testFile.js');
Crecket\DependencyManager\Registry::addCssFile('testFile.css');
```

In your twig template

```
<script src="/munee.php?files={{ getJsList('packer') }}">

<link href="/munee.php?files={{ getCssList('minify') }}" rel="stylesheet">
```