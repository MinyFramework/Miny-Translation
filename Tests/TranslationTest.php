<?php

namespace Modules\Translation;

class MockLoader implements iLoader
{

    public static function load($dir, $lang)
    {
        return array(
                'test'  => 'foo',
                'other' => 'bar'
            );
    }
}

class TranslationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Translation
     */
    private $translation;

    /**
     * @var array
     */
    private $config;

    public function setUp()
    {
        $this->config      = array(
            'language'  => 'test',
            'directory' => '.',
            'strings'   => array()
        );
        $this->translation = new Translation($this->config, '\\Modules\\Translation\\MockLoader');
    }

    public function testStringsAreAddedFromLoader()
    {
        $this->assertEquals('foo', $this->translation->get('test'));
        $this->assertEquals('bar', $this->translation->get('other'));
    }
}
