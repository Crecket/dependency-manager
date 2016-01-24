<?php
namespace Crecket\DependencyManager;

class Registry
{

    private static $jsFiles = array();
    private static $cssFiles = array();

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
     * @param $munee_location
     * @return string
     */
    public static function getJsLink($munee_location)
    {
        return $munee_location . "?files=" . implode(',', self::$jsFiles);
    }

    /**
     * @param $munee_location
     * @return string
     */
    public static function getCssLink($munee_location)
    {
        return $munee_location . "?files=" . implode(',', self::$cssFiles);
    }

}
