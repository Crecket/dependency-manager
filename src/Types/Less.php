<?php

Namespace Crecket\DependencyManager\Types;

use Crecket\DependencyManager\Utilities;

class Less implements Type
{

    private $file;
    private $cache;

    /**
     * Less constructor.
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
        // Check if this filepath/last_edit time hash is stored
        $fileHash = hash('sha256', $this->file['last_edit'] . $this->file['path']);

        // Check if cache contains this hash
        if ($this->cache->contains($fileHash)) {
            // Cache contains this combination so file hasn't moved/changed
            return $this->cache->fetch($fileHash);
        }

        // Create less parser
        $parser = new \Less_Parser();

        // Parse file using direct file path
        $parser->parseFile($this->file['path'], '/');

        // Turn less into css
        $css = $parser->getCss();

        // Store the css in the file system
        $this->cache->save($fileHash, $css);

        // return css
        return $css;
    }

}