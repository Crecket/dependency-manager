<?php

Namespace Crecket\DependencyManager;

class Loader
{

    private static $jsFiles = array();
    private static $cssFiles = array();
    private static $files = array();

    private static $secret = false;

    /**
     * @param $files
     * @param string $group
     */
    public static function addFiles($files, $group = 'default')
    {
        if (is_array($files)) {
            foreach ($files as $file) {
                self::$files[$group][] = $file;
            }
        } else {
            self::$files[$group][] = $files;
        }
    }

    /**
     * @param string $group
     */
    public static function removeFiles($group = 'default')
    {
        self::$files[$group] = array();
    }

    /**
     * @param $minify
     * @param string $group
     * @return string
     */
    public static function getFilesLink($minify = false, $group = 'default')
    {
        if (empty(self::$files[$group])) {
            self::$files[$group] = array();
        }
        // create hash for given file list and secret
        $hash = hash('sha256', serialize(self::$files[$group]) . self::$secret);

        // add to the link
        $link = "?secret=" . $hash;

        // now rehash again with the secret and store filelist in session
        $_SESSION['crecket_dependency_manager'][$hash] = self::$files[$group];

        // Add minify to link
        if ($minify === true) {
            $link .= "&minify=true";
        }

        return $link;

    }

    /**
     * @param $file
     */
    public static function addJsFile($file)
    {
        self::$jsFiles[] = $file;
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
        $_SESSION['crecket_dependency_manager'][$hash] = self::$jsFiles;

        // Add minify to link
        if ($minify === true) {
            $link .= "&minify=true";
        }

        return $link;
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
    public static function getCssLink($minify = false)
    {
        // create hash for given file list and secret
        $hash = hash('sha256', serialize(self::$cssFiles) . self::$secret);

        // add to the link
        $link = "?secret=" . $hash;

        // now rehash again with the secret and store filelist in session
        $_SESSION['crecket_dependency_manager'][$hash] = self::$cssFiles;

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
