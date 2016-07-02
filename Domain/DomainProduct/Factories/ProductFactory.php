<?php

namespace Domain\DomainProduct\Factories;

use Domain\DomainProduct\Objects\Search\ProductByStore\ProductSearch;
use Domain\DomainShared\Objects\Id;

/**
 * Created by PhpStorm.
 * User: albert
 * Date: 26/01/16
 * Time: 15:30
 */
class ProductFactory
{
    /**
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
     * @param $id
     * @return ProductSearch
     */
    public static function buildProductSearch(
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
        $hidePrice,
        $id = null
    ) {
        $id = new Id($id);
        return new ProductSearch(
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
        );
    }

}