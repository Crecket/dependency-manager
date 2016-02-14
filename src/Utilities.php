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
        self::$statusCodes[$code] = $message;
    }

    /**
     * @param $field
     * @param $value
     */
    public static function setHeader($field, $value)
    {
        self::$headers[$field] = $value;
    }

    /**
     * Send all collected headers
     */
    public static function sendHeaders()
    {
        foreach (self::$headers as $key => $header) {
            header($key . ": " . $header);
        }
        foreach (self::$statusCodes as $key => $header) {
            header("{$_SERVER['SERVER_PROTOCOL']} {$key} {$header}");
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