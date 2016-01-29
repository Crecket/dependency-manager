<?php

Namespace Crecket\DependencyManager;

final class Utilities
{

    /**
     * @param $code
     * @param $message
     */
    public static function statusCode($code, $message)
    {
        header("{$_SERVER['SERVER_PROTOCOL']} {$code} {$message}");
    }

    /**
     * @param $field
     * @param $value
     */
    public static function setHeader($field, $value)
    {
        header("{$field}: {$value}");
    }


    /**
     * @param $path
     * @return string
     */
    public static function getFile($path)
    {
        $file_contents = file_get_contents($path);

        return $file_contents;
    }

}