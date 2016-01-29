<?php

Namespace Crecket\DependencyManager;

final class Utilities
{

    private static $headers = array();
    private static $statusCodes = array();


    /**
     * @param $code
     * @param $message
     */
    public static function statusCode($code, $message)
    {
        self::$headers[] = ("{$_SERVER['SERVER_PROTOCOL']} {$code} {$message}");
    }

    /**
     * @param $field
     * @param $value
     */
    public static function setHeader($field, $value)
    {

        self::$headers[] = ("{$field}: {$value}");
    }

    /**
     * Send all collected headers
     */
    public static function sendHeaders()
    {
        foreach (self::$headers as $header) {
            header($header);
        }
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