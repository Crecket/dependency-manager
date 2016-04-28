<?php

Namespace Crecket\DependencyManager;

final class Utilities
{

    private static $headers = array();
    private static $statusCode = 200;
    private static $statusMessage = "";


    /**
     * @param $code
     * @param $message
     */
    public static function statusCode($code, $message)
    {
        self::$statusCode = $code;
        self::$statusMessage = $message;
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
        if (!headers_sent()) {
            foreach (self::$headers as $key => $header) {
                header($key . ": " . $header);
            }

            header("{$_SERVER['SERVER_PROTOCOL']} " . self::$statusCode . " " . self::$statusMessage);
        }
    }

    /**
     * @return array
     */
    public static function getHeaders()
    {
        return array('headers' => self::$headers, 'status_code' => self::$statusCode, 'status_message' => self::$statusMessage);
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