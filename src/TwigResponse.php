<?php

Namespace Crecket\DependencyManager;

class TwigResponse extends \Twig_Extension
{

    /**
     * @return string
     */
    public function getName()
    {
        return 'Dependency_Manager_Response';
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('getJsLink', function ($minify = false) {
                return Loader::getJsLink($minify);
            }),
            new \Twig_SimpleFunction('getCssLink', function ($minify = false) {
                return Loader::getCssLink($minify);
            }),
        );
    }

}
