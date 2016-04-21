<?php
require __DIR__ . '/../vendor/autoload.php';

class testMain extends PHPUnit_Framework_TestCase
{

    public function testLoading()
    {
        \Crecket\DependencyManager\Loader::addCssFiles(array(
            '/bower_components/bootstrap/less/bootstrap.less',
            '/bower_components/test.css'
        ));
        \Crecket\DependencyManager\Loader::addJsFiles(array(
            '/bower_components/jquery/dist/jquery.min.js',
            '/bower_components/bootstrap/dist/js/bootstrap.min.js'
        ));
    }

    public function testSecret()
    {
        \Crecket\DependencyManager\Loader::Secret('asdf');
    }

    public function testGetLinks()
    {
        $this->assertEquals(\Crecket\DependencyManager\Loader::getCssLink(true), '?secret=e22e067fbcda37f27bd551138a7d7b1e7b8146c5d7653d87f526b1d60114c895&minify=true');
        $this->assertEquals(\Crecket\DependencyManager\Loader::getCssLink(false), '?secret=e22e067fbcda37f27bd551138a7d7b1e7b8146c5d7653d87f526b1d60114c895');
        $this->assertEquals(\Crecket\DependencyManager\Loader::getJsLink(true), '?secret=27b3b42627444fe2c32914059d65c075f9a39375f2a96c9b2ae858cedee6928c&minify=true');
        $this->assertEquals(\Crecket\DependencyManager\Loader::getJsLink(false), '?secret=27b3b42627444fe2c32914059d65c075f9a39375f2a96c9b2ae858cedee6928c');
    }

    public function testResponse()
    {
        define('ROOT', __DIR__);

        $options = array(
            'Cache' => 'cache'
        );

        $jsList = array(
            '/../bower_components/jquery/dist/jquery.min.js',
            '/../bower_components/bootstrap/dist/js/bootstrap.min.js'
        );

        // test js loading/parsing
        $JsResponse = new \Crecket\DependencyManager\Response($options, $jsList);

        $cssList = array(
            '/../bower_components/bootstrap/less/bootstrap.less',
            '/../bower_components/bootstrap-sass/assets/stylesheets/_bootstrap.scss',
            '/../bower_components/bootstrap/dist/css/bootstrap.min.css'
        );

        // test css loading/parsing for css, less and sass
        $CssResponse = new \Crecket\DependencyManager\Response($options, $cssList);

    }

    // TODO Session testing

}