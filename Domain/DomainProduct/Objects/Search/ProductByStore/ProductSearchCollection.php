<?php

namespace Domain\DomainProduct\Objects\Search\ProductByStore;

use Domain\DomainProduct\Objects\ProductCollection;

class ProductSearchCollection extends ProductCollection
{

    /** @var AttributeSearchCountCollection */
    private $attributeCountCollection;

    /** @var int */
    private $totalSearchProducts;

    /**
     * ProductSearchCollection constructor.
     * @param AttributeSearchCountCollection $attributeCountCollection
     */
    public function __construct(AttributeSearchCountCollection $attributeCountCollection, $totalSearchProducts)
    {
        $this->attributeCountCollection = $attributeCountCollection;
        $this->totalSearchProducts = $totalSearchProducts;
    }

    /**
     * @return AttributeSearchCountCollection
     */
    public function attributeCountCollection()
    {
        return $this->attributeCountCollection;
    }

    /**
     * @return int
     */
    public function totalSearchProducts()
    {
        return $this->totalSearchProducts;
    }
}
