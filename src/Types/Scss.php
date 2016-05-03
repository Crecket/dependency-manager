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

        // get parsed files and store in cache
        $this->cache->save($this->file['hash'] . "parsed_files", $scss->getParsedFiles());

        // return css
        return $contents;

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
        return true;
    }

}