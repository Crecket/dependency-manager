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
        // TODO use secure file loader
        $file = Utilities::getFile($this->file['path']);
        $less = new \lessc;
        return $less->compile($file);
    }

}