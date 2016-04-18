<?php

Namespace Crecket\DependencyManager\Types;

use Crecket\DependencyManager\Utilities;

class Css implements Type
{

    private $file;
    private $cache;
    
    /**
     * Css constructor.
     * @param $file
     * @param $cache
     */
    public function __construct($file, $cache)
    {
        Utilities::setHeader('Content-Type', 'text/css');
        $this->file = $file;

        $this->cache = $cache;

        return $this;
    }

    /**
     * @return string
     */
    public function getFile()
    {
        // No point in storing in cache since no parsing is done
        return Utilities::getFile($this->file['path']);
    }

}