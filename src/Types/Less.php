<?php

Namespace Crecket\DependencyManager\Types;

use Crecket\DependencyManager\Utilities;

class Less implements Type
{

    private $file;

    /**
     * Less constructor.
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
        // TODO use caching from plugin instead of custom caching to avoid import errors
        // Create less parser
        $parser = new \Less_Parser();

        try {
            // Parse file using direct file path
            $parser->parseFile($this->file['path'], '/');

            // Turn less into css
            $contents = $parser->getCss();
        } catch (\Exception $e) {
            return false;
        }

        // fix absolute paths
        $contents = str_replace(array('../'), str_replace(ROOT, "", dirname($this->file['path'])) . '/../', $contents);

        // return css
        return $contents;
    }

}