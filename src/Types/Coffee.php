<?php

Namespace Crecket\DependencyManager\Types;

use Crecket\DependencyManager\Utilities;
use CoffeeScript\Compiler;

class Coffee implements Type
{

    private $file;

    /**
     * Coffee script constructor.
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
        // get file contents
        try {

            // get file contents
            $coffee = Utilities::getFile($this->file['path']);

            // ompile into js
            $js = Compiler::compile($coffee,
                array(
                    'filename' => basename($this->file['path']),
                )
            );
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        // return js
        return $js;

    }

}