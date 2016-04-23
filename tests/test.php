<?php
require __DIR__ . '/../vendor/autoload.php';

class testMain extends PHPUnit_Framework_TestCase
{

    public function testLoading()
    {
        Crecket\DependencyManager\Loader::addCssFiles(array(
            '/bower_components/bootstrap-sass/assets/stylesheets/_bootstrap.scss',
            '/bower_components/bootstrap/less/bootstrap.less',
            '/bower_components/test.css'
        ));

        Crecket\DependencyManager\Loader::addJsFiles(array(
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
        // TODO create better test case for link generation
        $this->assertNotEmpty(\Crecket\DependencyManager\Loader::getCssLink(true));
        $this->assertNotEmpty(\Crecket\DependencyManager\Loader::getCssLink(false));
        $this->assertNotEmpty(\Crecket\DependencyManager\Loader::getJsLink(true));
        $this->assertNotEmpty(\Crecket\DependencyManager\Loader::getJsLink(false));
    }

    public function testResponse()
    {
        define('ROOT', __DIR__);

        $options = array(
            'Cache' => ROOT.'/cache'
        );

        $jsList = array(
            '/../bower_components/jquery/dist/jquery.min.js',
            '/../bower_components/bootstrap/dist/js/bootstrap.min.js'
        );

        // test js loading/parsing
        $JsResponse = new \Crecket\DependencyManager\Response($options, $jsList);
        $this->assertNotEmpty($JsResponse->getResult());

        $cssList = array(
            '/../bower_components/bootstrap/less/bootstrap.less',
            '/../bower_components/bootstrap-sass/assets/stylesheets/_bootstrap.scss',
            '/../bower_components/bootstrap/dist/css/bootstrap.min.css'
        );

        // test css loading/parsing for css, less and sass
        $CssResponse = new \Crecket\DependencyManager\Response($options, $cssList);
        $this->assertNotEmpty($CssResponse->getResult());
    }
}