<?php

namespace Infrastructure\RepositoriesElasticSearch\SearchProduct;

use Domain\DomainProduct\Builders\ProductSearchBuilder;
use Domain\DomainProduct\Repositories\ProductSearchRepository;
use Domain\DomainProduct\Objects\Search\ProductByStore\ProductSearchCollection;

use \Elastica\Query as Query;
use \Elastica\Aggregation\Nested as AggregationNested;
use \Elastica\Aggregation\Terms as AggregationTerms;

/**
 * Created by PhpStorm.
 * User: albert
 * Date: 1/02/16
 * Time: 16:39
 */
class ElasticSearchProductSearchRepository extends ElasticSearchProductBaseRepository implements ProductSearchRepository
{

    const COUNT_NESTED_ATTRIBUTES = 'count_nested_attributes';
    const COUNT_ATTRIBUTES = 'count_attributes';
    const BUCKETS = 'buckets';

    const SIZE_NOT_LIMIT = 0;
    const ATTRIBUTES = 'attributesIds';
    const ATTRIBUTES_ID = 'attributesIds.id';
    const REL_ADDRESS = 'relAddress';
    const ID_FIELD = 'id';
    const TODAY_START = "today 00:00";
    const TODAY_END = "today 23:59";

    /**
     * @param $codeWebsite string
     * @param $idStore string
     * @param $attributes int[]
     * @param $page
     * @param $limit
     * @return ProductSearchCollection|null
     */
    public function SearchProductByStore($codeWebsite, $idStore, $attributes, $page, $limit)
    {
        $this->idStore = $idStore;
        $this->attributes = $attributes;
        $this->start = strtotime(self::TODAY_START);
        $this->end = strtotime(self::TODAY_END);

        $query = new Query();

        $queryFlash = new Query\Bool();
        $queryFlash->setParams($this->visibilitiesFlash());

        $queryPermanent = new Query\Bool();
        $queryPermanent->setParams($this->visibilitiesPermanent());

        $bool = new Query\Bool();
        $bool->addShould($queryFlash);
        $bool->addShould($queryPermanent);

        $nestedAgg = new AggregationNested(self::COUNT_NESTED_ATTRIBUTES, self::ATTRIBUTES);
        $termAgg = new AggregationTerms(self::COUNT_ATTRIBUTES);
        $termAgg->setField(self::ATTRIBUTES_ID);
        $termAgg->setSize(self::SIZE_NOT_LIMIT);
        $nestedAgg->addAggregation($termAgg);
        $query->addAggregation($nestedAgg);

        $query->setFrom($page * $limit);
        $query->setSize($limit);
        $query->setSort($this->setSort());

        $query->setQuery($bool);

        $result = $this->runQueryProduct($query, $codeWebsite);

        $listCount = $result->getAggregation(self::COUNT_NESTED_ATTRIBUTES);
        $listProduct = $result->getResults();
        $totalHits = $result->getTotalHits();

        $listIds = $this->getIdsPlaces($listProduct);
        $listPlaces = $this->getPlaces($listIds);

        return ProductSearchBuilder::buildProductSearchCollection($totalHits, $listProduct,
            $listCount[self::COUNT_ATTRIBUTES][self::BUCKETS], $listPlaces);
    }


    private function getPlaces($listIds)
    {
        $query = new Query();
        $bool = new Query\Bool();
        if (!empty($listIds)) {
            foreach ($listIds as $id ) {
                $newTerm = new Query\Term();
                $newTerm->setTerm(self::REL_ADDRESS, $id);
                $bool->addShould($newTerm);
            }
        }
        $query->setQuery($bool);
        $result = $this->runQueryPlace($query);

        return array_reduce(
            $result->getResults(),
            function ($result, $object) {
                isset($object->relAddress) && $result[$object->relAddress] = $object->url;
                return $result;
            },
            array()
        );

    }

    /**
     * @param $listProducts
     * @return string[]
     */
    private function getIdsPlaces($listProducts)
    {
        $listIds = array();
        foreach ($listProducts as $product) {
            if (!empty($product->locations) && is_array($listProducts)) {
                $listIdsProduct = $this->pluck(self::ID_FIELD, $product->locations);
                $listIds = array_merge($listIds, $listIdsProduct);
            }
        }
        return $listIds;
    }

    /**
     * Return array with value of key
     * @param $key
     * @param $data
     * @return mixed
     */
    public function pluck($key, $data)
    {
        return array_reduce(
            $data,
            function ($result, $object) use ($key) {
                isset($object[$key]) && $result[] = $object[$key];
                return $result;
            },
            array()
        );
    }


}