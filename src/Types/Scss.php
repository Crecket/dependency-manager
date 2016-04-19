<?php

Namespace Crecket\DependencyManager\Types;

use Crecket\DependencyManager\Utilities;

class Scss implements Type
{

    private $file;
    private $cache;

    /**
     * Scss constructor.
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
        // TODO verify that plugin has propper native caching support
        // Create less parser
        $scss = new \Leafo\ScssPhp\Compiler();

        // get file contents
        $file_contents = Utilities::getFile($this->file['path']);

        // Parse file using direct file path
        $css = $scss->compile($file_contents);

        // return css
        return $css;

    }

}