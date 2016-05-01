<?php

Namespace Crecket\DependencyManager;

class Loader
{

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
        // get hash value
        $hash = self::getHash($minify, $group);

        // add to the link
        return "?secret=" . $hash;
    }

    /**
     * @param bool $minify
     * @param string $group
     * @return mixed
     */
    public static function getHash($minify = false, $group = 'default')
    {
        // fallback in case no files are present
        if (empty(self::$files[$group])) {
            self::$files[$group] = array();
        }

        // create hash for given file list, minify option, group and secret
        $hash = hash('sha256', serialize(array(self::$files[$group], $minify, $group)) . self::$secret);

        // now rehash again with the secret and store filelist in session
        $_SESSION['crecket_dependency_manager'][$hash]['files'] = self::$files[$group];
        $_SESSION['crecket_dependency_manager'][$hash]['minify'] = $minify;

        return $hash;
    }

    /**
     * @param $secret
     */
    public static function Secret($secret)
    {
        self::$secret = $secret;
    }

}
