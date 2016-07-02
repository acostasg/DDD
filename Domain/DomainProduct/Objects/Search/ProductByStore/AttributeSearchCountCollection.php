<?php
/**
 * Created by PhpStorm.
 * User: albert
 * Date: 26/01/16
 * Time: 15:15
 */

namespace Domain\DomainProduct\Objects\Search\ProductByStore;

class AttributeSearchCountCollection implements \Iterator
{

    /** @var int */
    private $iterator;

    /** @var array */
    private $attributeCounts;

    public function construct()
    {
        $this->iterator = 0;
    }

    public function add(AttributeSearchCount $product)
    {
        $this->attributeCounts[$product->id()] = $product;
    }

    public function has($id){
        return (!empty($this->attributeCounts[$id]));
    }

    public function get($id){
        return $this->attributeCounts[$id];
    }

    public function current()
    {
        return $this->attributeCounts[$this->iterator];
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
        return isset($this->attributeCounts[$this->iterator]);
    }

    public function rewind()
    {
        $this->iterator = 0;
    }
}