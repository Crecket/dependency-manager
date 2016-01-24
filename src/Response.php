<?php

Namespace Crecket\DependencyManager;

class Response extends \Twig_Extension
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
            new \Twig_SimpleFunction('getJsLink', function ($minify) {
                return Registry::getJsLink($minify);
            }),
            new \Twig_SimpleFunction('getCssLink', function ($minify) {
                return Registry::getCssLink($minify);
            }),
        );
    }

}
