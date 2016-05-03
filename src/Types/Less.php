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
            $contents = $parser->getCss();

            // get all parsed files
            $parsed_files = $parser::AllParsedFiles();

            // reformat to make them the same format as the scss result parsed files list
            $new_list = array();
            foreach ($parsed_files as $parse_file) {
                $new_list[$parse_file] = filemtime($parse_file);
            }

            // store the new list
            $this->cache->save($this->file['hash'] . "parsed_files", $new_list);

        } catch (\Exception $e) {
            return false;
        }

        // fix absolute paths
        $contents = str_replace(array('../'), str_replace(ROOT, "", dirname($this->file['path'])) . '/../', $contents);

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