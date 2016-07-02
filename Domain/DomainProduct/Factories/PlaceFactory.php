<?php

namespace Domain\DomainProduct\Factories;

use Domain\DomainProduct\Objects\Search\ProductByStore\PlaceSearch;
use Domain\DomainShared\Objects\Id;

/**
 * Created by PhpStorm.
 * User: albert
 * Date: 26/01/16
 * Time: 15:30
 */
class PlaceFactory
{
    /**
     * @param $url
     * @param $name
     * @param $id
     * @return PlaceSearch
     */
    public static function buildPlaceSearch(
        $name,
        $url = null,
        $id = null
    ) {
        $id = new Id($id);
        return new PlaceSearch($id, $url, $name);
    }

}