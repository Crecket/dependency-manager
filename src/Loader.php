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
        // create hash for given file list and secret
        $hash = hash('sha256', serialize(self::$jsFiles) . self::$secret);

        // add to the link
        $link = "?secret=" . $hash;

        // now rehash again with the secret and store filelist in session
        $_SESSION['dependency_test'][hash('sha256', $hash . self::$secret)] = self::$jsFiles;

        // Add minify to link
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
        // create hash for given file list and secret
        $hash = hash('sha256', serialize(self::$cssFiles) . self::$secret);

        // add to the link
        $link = "?secret=" . $hash;

        // now rehash again with the secret and store filelist in session
        $_SESSION['dependency_test'][hash('sha256', $hash . self::$secret)] = self::$cssFiles;

        // Add minify to link
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
