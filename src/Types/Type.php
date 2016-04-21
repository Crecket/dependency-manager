<?php

Namespace Crecket\DependencyManager\Types;

interface Type
{

    // Construct with the file's info and a cache object
    public function __construct($file);

    // Return the file contents
    public function getFile();

}