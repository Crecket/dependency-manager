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

        // TODO use caching from plugin instead of custom caching to avoid import errors
        // Create less parser
        $parser = new \Less_Parser();

        try {
            // Parse file using direct file path
            $parser->parseFile($this->file['path'], '/');

            // Turn less into css
            $css = $parser->getCss();
        } catch (\Exception $e) {
            return false;
        }

        // return css
        return $css;
    }

}