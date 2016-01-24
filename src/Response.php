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
            new \Twig_SimpleFunction('getJsLink', function ($munee_location) {
                return Registry::getJsLink($munee_location);
            }),
            new \Twig_SimpleFunction('getCssLink', function ($munee_location) {
                return Registry::getCssLink($munee_location);
            }),
        );
    }

}
