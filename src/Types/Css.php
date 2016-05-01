<?php

Namespace Crecket\DependencyManager\Types;

use Crecket\DependencyManager\Utilities;

class Css implements Type
{

    private $file;

    /**
     * Css constructor.
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
        // No point in storing in cache since no parsing is done
        $contents = Utilities::getFile($this->file['path']);

        // fix absolute paths
        $contents = str_replace(array('../'), str_replace(ROOT, "", dirname($this->file['path'])) . '/../', $contents);

        return $contents;
    }

}