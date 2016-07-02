<?php

namespace Application\ApplicationShared\Responses;

use Domain\DomainMenus\Objects\MainMenuCollection;
use Domain\DomainProduct\Objects\Search\ProductByStore\ProductSearchCollection;
use Domain\DomainProduct\Objects\AttributeCollection;

/**
 * Created by PhpStorm.
 * User: albert
 * Date: 26/01/16
 * Time: 16:09
 */
class SearchProductByStoreResponse
{
    /** @var productSearchCollection */
    private $productSearchCollection;

    /** @var int */
    private $actualPage;

    /** @var int */
    private $totalPage;

    /** @var MainMenuCollection */
    private $subMenuCollection;

    /** @var string */
    private $store;

    /** @var string[] */
    private $attributes;

    /**
     * SearchProductByStoreResponse constructor.
     * @param ProductSearchCollection $productSearchCollection
     * @param int $actualPage
     * @param int $totalPage
     * @param MainMenuCollection $subMenuCollection
     * @param string $store
     * @param string[] $attributes
     */
    public function __construct(
        ProductSearchCollection $productSearchCollection,
        $actualPage,
        $totalPage,
        $subMenuCollection,
        $store,
        $attributes
    ) {
        $this->productSearchCollection = $productSearchCollection;
        $this->actualPage = $actualPage;
        $this->totalPage = $totalPage;
        $this->subMenuCollection = $subMenuCollection;
        $this->store = $store;
        $this->attributes = $attributes;
    }

    /**
     * @return int
     */
    public function totalSearchProducts()
    {
        return $this->productSearchCollection->totalSearchProducts();
    }

    /**
     * @return ProductSearchCollection
     */
    public function productSearchCollection(){
        return $this->productSearchCollection;
    }

    /**
     * @return int
     */
    public function actualPage()
    {
        return $this->actualPage;
    }

    /**
     * @return int
     */
    public function totalPage()
    {
        return $this->totalPage;
    }

    /**
     * @return MainMenuCollection
     */
    public function subMenuCollection()
    {
        return $this->subMenuCollection;
    }

    /**
     * @return string
     */
    public function store()
    {
        return $this->store;
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return $this->attributes;
    }

}