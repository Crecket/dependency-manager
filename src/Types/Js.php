<?php

Namespace Crecket\DependencyManager\Types;

use Crecket\DependencyManager\Utilities;

class Js implements Type
{

    private $file;

    /**
     * Js constructor.
     * @param $file
     * @param $cache
     */
    public function __construct($file, $cache)
    {
        Utilities::setHeader('Content-Type', 'application/javascript');
        $this->file = $file;
        
        return $this;
    }

    /**
     * @return string
     */
    public function getFile()
    {
        // No point in caching js files since no parsing is done
        return Utilities::getFile($this->file['path']);
    }

    /**
     * @return mixed
     */
    public function getFileInfo()
    {
        return $this->file;
    }

    /**
     * @return bool
     */
    public function requiresFileList()
    {
        return false;
    }

}