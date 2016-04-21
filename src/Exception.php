<?php

namespace Crecket\DependencyManager;

class Exception extends \Exception
{
    protected $title;

    public function __construct($title, $message, $code = 500, Exception $previous = null)
    {
        $this->title = $title;

        parent::__construct($message, $code, $previous);

        Utilities::statusCode($code, $title);
        Utilities::sendHeaders();
    }

    public function getTitle()
    {
        return $this->title;
    }
}
