<?php

namespace Domain\DomainProduct\Objects\Search\ProductByStore;

use Domain\Place\Search\Service\PlacesSearch;

class ProductSearch
{
    /**
     * ProductSearch constructor.
     * @param $id
     * @param $title
     * @param $image
     * @param $discount
     * @param $place
     * @param $tradeName
     * @param $specialPrice
     * @param $locationSummary
     * @param $review
     * @param $shortTitle
     * @param $url
     * @param $hidePrice
     */
    public function __construct(
        $id,
        $title,
        $image,
        $discount,
        $place,
        $tradeName,
        $specialPrice,
        $locationSummary,
        $review,
        $shortTitle,
        $url,
        $hidePrice
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->image = $image;
        $this->discount = $discount;
        $this->place = $place;
        $this->tradeName = $tradeName;
        $this->specialPrice = $specialPrice;
        $this->locationSummary = $locationSummary;
        $this->review = $review;
        $this->shorTitle = $shortTitle;
        $this->url = $url;
        $this->hidePrice = $hidePrice;
    }

    /** @var  int */
    private $id;

    /** @var string */
    private $title;

    /** @var ImageSearch */
    private $image;

    /** @var  int */
    private $discount;

    /** @var PlacesSearch */
    private $place;

    /** @var string */
    private $tradeName;

    /** @var  float */
    private $specialPrice;

    /** @var  array */
    private $locationSummary;

    /** @var  ProductReview */
    private $review;

    /** @var  string */
    private $shorTitle;

    /** @var  string */
    private $url;

    /** @var  bool */
    private $hidePrice;

    /**
     * @return int
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function title()
    {
        return $this->title;
    }

    /**
     * @return ImageSearch
     */
    public function image()
    {
        return $this->image;
    }

    /**
     * @return int
     */
    public function discount()
    {
        return $this->discount;
    }

    /**
     * @return PlacesSearch
     */
    public function place()
    {
        return $this->place;
    }

    /**
     * @return string
     */
    public function tradeName()
    {
        return $this->tradeName;
    }

    /**
     * @return float
     */
    public function specialPrice()
    {
        return $this->specialPrice;
    }

    /**
     * @return string
     */
    public function locationSummary()
    {
        return $this->locationSummary;
    }

    /**
     * @return ProductReview
     */
    public function review()
    {
        return $this->review;
    }

    /**
     * @return string
     */
    public function shorTitle()
    {
        return $this->shorTitle;
    }

    /**
     * @return string
     */
    public function url()
    {
        return $this->url;
    }

    /**
     * @return boolean
     */
    public function hidePrice()
    {
        return $this->hidePrice;
    }

}