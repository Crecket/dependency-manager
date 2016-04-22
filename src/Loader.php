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
        self::$jsFiles[] = array('local' => true, 'type' => 'js', 'location' => $file);
    }

    /**
     * @param $file
     */
    public static function addCssFile($file)
    {
        self::$cssFiles[] = array('local' => true, 'type' => 'css', 'location' => $file);
    }

    /**
     * @param $files
     */
    public static function addJsFiles($files)
    {
        foreach ($files as $file) {
            self::$jsFiles[] = array('local' => true, 'type' => 'js', 'location' => $file);
        }
    }

    /**
     * @param $files
     */
    public static function addCssFiles($files)
    {
        foreach ($files as $file) {
            self::$cssFiles[] = array('local' => true, 'type' => 'css', 'location' => $file);
        }
    }

    /**
     * @param $type
     * @param $file
     * @return bool
     * @throws Exception
     */
    public static function addRemoteFile($type, $file)
    {

        if (strtolower($type) === 'css') {
            self::$cssFiles[] = array('local' => false, 'type' => 'css', 'location' => $file);
            return true;
        }

        if (strtolower($type) === 'js') {
            self::$jsFiles[] = array('local' => false, 'type' => 'js', 'location' => $file);
            return true;
        }

        throw new Exception('Invalid type', 'Input type is not \'js\' or \'css\'');
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
