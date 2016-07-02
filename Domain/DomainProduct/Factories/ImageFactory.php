<?php

namespace Domain\DomainProduct\Factories;

use Domain\DomainProduct\Objects\Search\ProductByStore\ImageSearch;
use Domain\DomainShared\Objects\Id;

/**
 * Created by PhpStorm.
 * User: albert
 * Date: 26/01/16
 * Time: 15:30
 */
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