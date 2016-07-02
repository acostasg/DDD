<?php

namespace Domain\DomainProduct\Objects;

use Domain\DomainProduct\Objects\Search\ProductByStore\ProductSearch;

class ProductCollection implements \Iterator
{

    /** @var  int */
    private $iterator;

    /**
     * @var Product[]
     */
    private $products;

    public function construct()
    {
        $this->iterator = 0;
    }

    public function add(ProductSearch $product)
    {
        $this->products[] = $product;
    }

    public function current()
    {
        return $this->products[$this->iterator];
    }

    public function next()
    {
        $this->iterator++;
    }

    public function key()
    {
        return $this->iterator;
    }

    public function valid()
    {
        return isset($this->products[$this->iterator]);
    }

    public function rewind()
    {
        $this->iterator = 0;
    }
}
