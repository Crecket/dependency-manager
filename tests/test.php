<?php
require __DIR__ . '/../vendor/autoload.php';

// TODO bower install: https://github.com/sebastianbergmann/phpunit-website

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
        $this->assertEquals(\Crecket\DependencyManager\Loader::getCssLink(true), '?secret=3bfe9df10b476c2454285e68629432a1002e87d943c169d378b28c361e8eface&minify=true');
        $this->assertEquals(\Crecket\DependencyManager\Loader::getCssLink(false), '?secret=3bfe9df10b476c2454285e68629432a1002e87d943c169d378b28c361e8eface');
        $this->assertEquals(\Crecket\DependencyManager\Loader::getJsLink(true), '?secret=6e424af764f239830b0e706b0422318bd4572b7caeb9921b198ad6c97d82621e&minify=true');
        $this->assertEquals(\Crecket\DependencyManager\Loader::getJsLink(false), '?secret=6e424af764f239830b0e706b0422318bd4572b7caeb9921b198ad6c97d82621e');
    }

    public function testResponse()
    {
        define('ROOT', __DIR__);

        $options = array(
            'Cache' => ROOT.'/cache',
            'Remote_storage' => ROOT.'/bower_components/'
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

    // TODO Session testing

}