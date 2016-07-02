<?php

namespace Domain\DomainProduct\Repositories;

use Domain\DomainProduct\Objects\Search\ProductByStore\ProductSearchCollection;

interface ProductSearchRepository
{
    /**
     * @param $codeWebsite string
     * @param $store
     * @param $attributes
     * @param $page
     * @param $limit
     * @return ProductSearchCollection|null
     */
    public function SearchProductByStore($codeWebsite, $store, $attributes, $page, $limit);
}
