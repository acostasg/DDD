<?php
/**
 * Created by PhpStorm.
 * User: albert
 * Date: 26/01/16
 * Time: 18:20
 */

namespace Domain\DomainProduct\Objects\Search\ProductByStore;


use Domain\DomainShared\Objects\Id;

class AttributeSearchCount
{
    /** @var  Id */
    private $id;

    /** @var int */
    private $count;

    /**
     * AttributeCount constructor.
     * @param Id $id
     * @param int $count
     */
    public function __construct(Id $id, $count)
    {
        $this->id;
        $this->count = $count;
    }

    public function id(){
        return $this->id;
    }

    public function count(){
        return $this->count;
    }

}