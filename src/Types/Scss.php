<?php

Namespace Crecket\DependencyManager\Types;

use Crecket\DependencyManager\Utilities;

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
        // TODO use secure file loader
        $scss = new \Leafo\ScssPhp\Compiler();

        $file_contents = Utilities::getFile($this->file['path']);
        return $scss->compile($file_contents);
    }

}