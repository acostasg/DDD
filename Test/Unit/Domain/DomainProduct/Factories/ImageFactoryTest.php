<?php

namespace Test\Unit\Domain\DomainProduct\Factories;

use Domain\DomainProduct\Factories\ImageFactory;
use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;

class ImageFactoryTest extends PHPUnit_Framework_TestCase
{

    private $id = 'idTest';
    private $url = 'urlTest';
    private $alt = 'altTest';
    private $className = 'ImageSearch';

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers ImageFactory::buildImageSearch
     * @group unit
     */
    public function testBuildImageSearch()
    {
        $imageObject = ImageFactory::buildImageSearch($this->id, $this->url, $this->alt);

        $this->assertEquals($imageObject->id(), $this->id);

        $this->assertEquals($imageObject->alt(), $this->alt);

        $this->assertEquals($imageObject->url(), $this->url);
    }
}
