<?php

namespace Test\Unit\Domain\DomainProduct\Factories;

/**
 * Created by PhpStorm.
 * User: albert
 * Date: 28/01/16
 * Time: 12:22
 */

use Domain\DomainProduct\Factories\PlaceFactory;
use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;

class PlaceFactoryTest extends PHPUnit_Framework_TestCase
{

    private $id = 'idTest';
    private $url = 'urlTest';
    private $name = 'altTest';
    private $className = 'PlaceSearch';

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
     * @covers PlaceFactory::buildPlaceSearch
     * @group unit
     */
    public function testBuildPlaceSearch()
    {
        $placeObject = PlaceFactory::buildPlaceSearch($this->id, $this->url, $this->name);

        $this->assertEquals($placeObject->id(), $this->id);

        $this->assertEquals($placeObject->url(), $this->url);

        $this->assertEquals($placeObject->name(), $this->name);

    }

}
