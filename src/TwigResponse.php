<?php

Namespace Crecket\DependencyManager;

class TwigResponse extends \Twig_Extension
{

    /**
     * @return string
     */
    public function getName()
    {
        return 'Crecket_Dependency_Manager_Response';
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('GetJsLink', function ($minify = false) {
                return Loader::getJsLink($minify);
            }),
            new \Twig_SimpleFunction('GetCssLink', function ($minify = false) {
                return Loader::getCssLink($minify);
            }),
            new \Twig_SimpleFunction('GetFilesLink', function ($minify = false, $group = 'default') {
                return Loader::getFilesLink($minify, $group);
            }),
            new \Twig_SimpleFunction('GetFilesHash', function ($minify = false, $group = 'default') {
                return Loader::getHash($minify, $group);
            }),
        );
    }

}
