<?php

namespace Modules\Translation;

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
            'language' => 'test',
            'strings'  => array()
        );
        $this->translation = new Translation($this->config);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAnExceptionIsThrownWhenTranslationFileIsNotFound()
    {
        new Translation(array(
            'language'  => 'test',
            'directory' => '.',
            'strings'   => array()
        ));
    }

    public function testStringsAreAdded()
    {
        $this->translation->addString('test', 'foo');
        $this->translation->addStrings(array('other' => 'bar'));
        $this->assertEquals('foo', $this->translation->get('test'));
        $this->assertEquals('bar', $this->translation->get('other'));
    }
}
