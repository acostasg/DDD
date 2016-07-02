<?php

namespace Test\Unit\Domain\DomainProduct\Factories;

use Domain\DomainProduct\Factories\ProductFactory;
use Domain\DomainProduct\Objects\Search\ProductByStore\PlaceSearch;
use Domain\DomainProduct\Objects\Search\ProductByStore\ProductSearchReview;
use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;

class ProductSearchFactoryTest extends PHPUnit_Framework_TestCase
{

    /** @var string */
    private $id = 'testId';

    /** @var string */
    private $title = 'testTitle';

    /** @var ImageSearch */
    private $image = 'testImage';

    /** @var  int */
    private $discount = -18;

    /** @var PlacesSearch */
    private $place;

    /** @var string */
    private $tradeName = 'testTradeName';

    /** @var  float */
    private $specialPrice = 'testSpecialPrice';

    /** @var  string */
    private $locationSummary = 'testLocationSummary';

    /** @var  ProductReview */
    private $review;

    /** @var  string */
    private $shorTitle = 'testShorTitle';

    /** @var  string */
    private $url = 'testUrl';

    /** @var  bool */
    private $hidePrice = false;

    private $noTest = 'noTest';

    private $className = 'ProductSearch';

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->place = new PlaceSearch($this->noTest, $this->noTest, $this->noTest);
        $this->review = new ProductSearchReview($this->noTest, $this->noTest, $this->noTest, $this->noTest);
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
        $placeObject = ProductFactory::buildProductSearch($this->id, $this->title, $this->image, $this->discount,
            $this->place, $this->tradeName, $this->specialPrice, $this->locationSummary, $this->review,
            $this->shorTitle, $this->url, $this->hidePrice);

        $this->assertEquals($placeObject->id(), $this->id);

        $this->assertEquals($placeObject->title(), $this->title);

        $this->assertEquals($placeObject->image(), $this->image);

        $this->assertEquals($placeObject->discount(), $this->discount);

        $this->assertEquals($placeObject->place(), $this->place);

        $this->assertEquals($placeObject->tradeName(), $this->tradeName);

        $this->assertEquals($placeObject->specialPrice(), $this->specialPrice);

        $this->assertEquals($placeObject->specialPrice(), $this->specialPrice);

        $this->assertEquals($placeObject->locationSummary(), $this->locationSummary);

        $this->assertEquals($placeObject->review(), $this->review);

        $this->assertEquals($placeObject->review(), $this->review);

        $this->assertEquals($placeObject->shorTitle(), $this->shorTitle);

        $this->assertEquals($placeObject->url(), $this->url);

        $this->assertEquals($placeObject->hidePrice(), $this->hidePrice);
    }
}
