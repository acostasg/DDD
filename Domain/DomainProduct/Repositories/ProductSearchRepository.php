<?php

namespace Domain\DomainProduct\Repositories;

use Domain\DomainProduct\Objects\Search\ProductByStore\ProductSearchCollection;

/**
 * Created by PhpStorm.
 * User: albert
 * Date: 26/01/16
 * Time: 16:04
 */
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