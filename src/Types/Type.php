<?php

Namespace Crecket\DependencyManager\Types;

interface Type
{

    // Construct with the file's info and a cache object
    public function __construct($file, $cache);

    // Return the file contents
    public function getFile();

    // Only get the file info
    public function getFileInfo();

    // Whether this type needs to have a stored file list, if none is found we will have to recompile
    public function requiresFileList();

}