<?php
/**
 * Created by PhpStorm.
 * User: albert
 * Date: 28/01/16
 * Time: 10:52
 */

namespace Domain\DomainProduct\Objects\Search\ProductByStore;


class PlaceSearch
{

    /**
     * PlaceSearch constructor.
     * @param $id
     * @param $url
     * @param $name
     */
    public function __construct($id,$url,$name)
    {
        $this->id = $id;
        $this->url = $url;
        $this->name = $name;
    }

    /** @var string */
    private $id;

    /** @var string */
    private $url;

    /** @var string */
    private $name;

    /**
     * @return string
     */
    public function id(){
        return $this->id;
    }

    /**
     * @return string
     */
    public function url(){
        return $this->url;
    }

    /**
     * @return string
     */
    public function name(){
        return $this->name;
    }
}