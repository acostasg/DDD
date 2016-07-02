<?php

namespace Domain\DomainProduct\Objects\Search\ProductByStore;

class ImageSearch
{

    /**
     * ImageSearch constructor.
     * @param $id
     * @param $url
     * @param $alt
     */
    public function __construct($id, $url, $alt)
    {
        $this->id = $id;
        $this->url = $url;
        $this->alt = $alt;
    }

    /**
     * @return string
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function url()
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function alt()
    {
        return $this->alt;
    }

    /** @var string */
    private $id;

    /** @var  string */
    private $url;

    /** @var  string */
    private $alt;
}
