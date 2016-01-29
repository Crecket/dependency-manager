<?php

Namespace Crecket\DependencyManager\Types;

use Crecket\DependencyManager\Utilities;

class Js implements Type
{

    private $file;

    /**
     * Js constructor.
     * @param $file
     */
    public function __construct($file)
    {
        Utilities::setHeader('Content-Type', 'application/javascript');
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