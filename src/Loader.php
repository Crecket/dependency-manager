<?php

Namespace Crecket\DependencyManager;

class Loader
{

    private static $jsFiles = array();
    private static $cssFiles = array();

    private static $secret = false;

    /**
     * @param $file
     */
    public static function addJsFile($file)
    {
        self::$jsFiles[] = $file;
    }

    /**
     * @param $file
     */
    public static function addCssFile($file)
    {
        self::$cssFiles[] = $file;
    }

    /**
     * @param $files
     */
    public static function addJsFiles($files)
    {
        foreach ($files as $file) {
            self::$jsFiles[] = $file;
        }
    }

    /**
     * @param $files
     */
    public static function addCssFiles($files)
    {
        foreach ($files as $file) {
            self::$cssFiles[] = $file;
        }
    }

    /**
     * @param $minify
     * @return string
     */
    public static function getJsLink($minify = false)
    {
        $list = implode(',', self::$jsFiles);

        $link = "?files=" . $list;

        if (self::$secret !== false) {
            $link .= "&secret=" . hash('sha256', $list . self::$secret);
        }

        if ($minify === true) {
            $link .= "&minify=true";
        }

        return $link;
    }

    /**
     * @param $minify
     * @return string
     */
    public static function getCssLink($minify = false)
    {
        $list = implode(',', self::$cssFiles);

        $link = "?files=" . $list;

        if (self::$secret !== false) {
            $link .= "&secret=" . hash('sha256', $list . self::$secret);
        }

        if ($minify === true) {
            $link .= "&minify=true";
        }

        return $link;
    }

    /**
     * @param $secret
     */
    public static function Secret($secret)
    {
        self::$secret = $secret;
    }

}
