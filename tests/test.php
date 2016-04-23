<?php
require __DIR__ . '/../vendor/autoload.php';

// TODO bower install: https://github.com/sebastianbergmann/phpunit-website

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
        $this->assertEquals(\Crecket\DependencyManager\Loader::getCssLink(true), '?secret=ca48b5ff1fc8d475b138b336e49e0d2adc3bb11ef5546ac855045be2a17957a5&minify=true');
        $this->assertEquals(\Crecket\DependencyManager\Loader::getCssLink(false), '?secret=ca48b5ff1fc8d475b138b336e49e0d2adc3bb11ef5546ac855045be2a17957a5');
        $this->assertEquals(\Crecket\DependencyManager\Loader::getJsLink(true), '?secret=e195b9eaeaa09cab85600a86bfb7e88b34903088146706600191355b4191af0a&minify=true');
        $this->assertEquals(\Crecket\DependencyManager\Loader::getJsLink(false), '?secret=e195b9eaeaa09cab85600a86bfb7e88b34903088146706600191355b4191af0a');
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