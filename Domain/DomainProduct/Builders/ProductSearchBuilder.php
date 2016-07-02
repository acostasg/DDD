<?php

namespace Domain\DomainProduct\Builders;

use Domain\DomainProduct\Factories\AttributeSearchCountFactory;
use Domain\DomainProduct\Factories\ImageFactory;
use Domain\DomainProduct\Factories\PlaceFactory;
use Domain\DomainProduct\Factories\ProductFactory;
use Domain\DomainProduct\Objects\Search\ProductByStore\AttributeSearchCountCollection;
use Domain\DomainProduct\Objects\Search\ProductByStore\ProductSearchCollection;

class ProductSearchBuilder
{

    /**
     * @param $totalSearchProducts
     * @param  array                   $listProducts
     * @param  array                   $listAttributeSearchCount
     * @param  array                   $listPlace
     * @return ProductSearchCollection
     * @throws \Exception
     */
    public static function buildProductSearchCollection(
        $totalSearchProducts,
        $listProducts,
        $listAttributeSearchCount,
        $listPlace
    ) {
        $attributesCountCollection = new AttributeSearchCountCollection();

        foreach ($listAttributeSearchCount as $item) {
            $item = AttributeSearchCountFactory::build(
                $item['key'],
                $item['doc_count']
            );
            $attributesCountCollection->add($item);
        }

        $collection = new ProductSearchCollection(
            $attributesCountCollection,
            $totalSearchProducts
        );

        foreach ($listProducts as $product) {
            $img = ImageFactory::buildImageSearch(
                $product->img,
                $product->shortTitle
            );

            $url = null;
            if (!empty($product->locations) && is_array($product->locations)) {
                foreach ($product->locations as $location) {
                    if (isset($listPlace[$location['id']])) {
                        //TODO url absolute
                        $url = 'partners/'.$listPlace[$location['id']];
                        break;
                    }
                }
            }

            $place = PlaceFactory::buildPlaceSearch(
                $product->tradeName,
                $url
            );

            $listPrice = $product->prices;

            $item = ProductFactory::buildProductSearch(
                $product->title,
                $img,
                $listPrice['discount'],
                $place,
                $product->tradeName,
                $listPrice['specialPrice'],
                $product->locationSummary,
                $product->review,
                $product->shortTitle,
                $product->urlKey,
                $listPrice['hidePrice'],
                $product->id
            );
            $collection->add($item);
        }

        return $collection;
    }
}
