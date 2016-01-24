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
     * @param $minify
     * @return string
     */
    public static function getJsLink($minify)
    {
        $link = implode(',', self::$jsFiles);

        if($minify === 'minify'){
            $link .= "&minify=true";
        }else if ($minify === 'packer'){
            $link .= "&packer=true";
        }

        return $link;
    }

    /**
     * @param $minify
     * @return string
     */
    public static function getCssLink($minify)
    {
        $link = implode(',', self::$cssFiles);

        if($minify === 'minify'){
            $link .= "&minify=true";
        }

        return $link;
    }

}
