<?php

Namespace Crecket\DependencyManager\Types;

use Crecket\DependencyManager\Utilities;
use Leafo\ScssPhp\Compiler;

class Scss implements Type
{

    private $file;

    /**
     * Scss constructor.
     * @param $file
     */
    public function __construct($file)
    {
        Utilities::setHeader('Content-Type', 'text/css');
        $this->file = $file;

        return $this;
    }

    /**
     * @return string
     */
    public function getFile()
    {
        // get file contents
        $file_contents = Utilities::getFile($this->file['path']);

        // Create scss parser
        $scss = new Compiler();

        // set load path for imported files
        $scss->setImportPaths(dirname($this->file['path']));

        // Parse file using direct file path
        $contents = $scss->compile($file_contents);

        // fix absolute file paths
        $contents = str_replace(array('../'), str_replace(ROOT, "", dirname($this->file['path'])) . '/../', $contents);

        // return css
        return $contents;

    }

}