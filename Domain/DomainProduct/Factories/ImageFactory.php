<?php

namespace Domain\DomainProduct\Factories;

use Domain\DomainProduct\Objects\Search\ProductByStore\ImageSearch;
use Domain\DomainShared\Objects\Id;

class ImageFactory
{
    /**
     * @param $url
     * @param $alt
     * @param $id
     * @return ImageSearch
     */
    public static function buildImageSearch(
        $url,
        $alt,
        $id = null
    ) {
        $id = new Id($id);

        return new ImageSearch($id, $url, $alt);
    }
}
