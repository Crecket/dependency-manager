<?php

Namespace Crecket\DependencyManager\Types;

use Crecket\DependencyManager\Utilities;
use Leafo\ScssPhp\Compiler;

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

        // get file contents
        $file_contents = Utilities::getFile($this->file['path']);

        // Create scss parser
        $scss = new Compiler();

        // set load path for imported files
        $scss->setImportPaths(dirname($this->file['path']));

        // Parse file using direct file path
        $css = $scss->compile($file_contents);

        // return css
        return $css;

    }

}