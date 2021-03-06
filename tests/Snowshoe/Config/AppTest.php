<?php
/**
 *
 * @author Ed van Beinum <e@edvanbeinum.com>
 * @version $Id$
 * @copyright Ibuildings 07/08/2011
 * @package Snowshoe
 */
require_once dirname(__FILE__) . '/../../../Snowshoe/bootstrap.php';

/**
 *
 * @package SnowshoeTest
 * @author Ed van Beinum <e@edvanbeinum.com>
 */
class AppTest extends PHPUnit_Framework_TestCase
{
    protected $_app;

    public function setUp()
    {
        $this->_app = new \Snowshoe\Config\App();
    }

    public function tearDown()
    {
        unset($this->_app);
    }

    /**
     * @test
     */
    public function getter_returns_set_value()
    {
        $expected = array('one' => 'test', 'two' => 'lipsum');
        $this->_app->setConfig($expected);
        $this->assertSame($expected, $this->_app->getConfig());
    }

    /**
     * @test
     * @return void
     */
    public function magic_getter_returns_array_values()
    {
        $expected = array('one' => 'test', 'two' => 'lipsum');
        $this->_app->setConfig($expected);
        $this->assertSame('test', $this->_app->getOne());
    }

    /**
     * @test
     */
    public function magic_getter_translates_underscores_into_camel_case()
    {
        $expected = array('multi_word_var' => 'test',);
        $this->_app->setConfig($expected);
        $this->assertSame('test', $this->_app->getMultiWordVar());
    }

    /**
     * @test
     */
    public function setConfigValues_adds_key_and_value_to_config()
    {
        $config = array('one' => 'test', 'two' => 'lipsum');
        $newConfig = array('three' => 'lorem');
        $expected = array('one' => 'test', 'two' => 'lipsum', 'three' => 'lorem');

        $this->_app->setConfig($config);
        $this->_app->setConfigValue($newConfig);


        $this->assertSame($expected, $this->_app->getConfig());
    }

     /**
     * @test
     */
    public function setConfigValues_updates_key_and_value_to_config()
    {
        $config = array('one' => 'test', 'two' => 'lipsum');
        $newConfig = array('two' => 'new value');
        $expected = array('one' => 'test', 'two' => 'new value');

        $this->_app->setConfig($config);
        $this->_app->setConfigValue($newConfig);


        $this->assertSame($expected, $this->_app->getConfig());
    }



    /**
     * @test
     * @expectedException ErrorException
     */
    public function config_throws_exception_when_value_does_not_exist()
    {
        $config = array(
            'test' => 'value'
        );
        $this->_app->setConfig($config);
        $this->_app->getNonExistentValue();
    }

}