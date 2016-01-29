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
        // TODO use secure file loader
        return Utilities::getFile($this->file['path']);
    }

}