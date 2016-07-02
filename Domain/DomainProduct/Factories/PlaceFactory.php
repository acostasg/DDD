<?php

namespace Domain\DomainProduct\Factories;

use Domain\DomainProduct\Objects\Search\ProductByStore\PlaceSearch;
use Domain\DomainShared\Objects\Id;

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
