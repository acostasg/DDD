<?php

namespace Infrastructure\Product\Search\Indexer\Service;

use Infrastructure\Product\Search\Indexer\Repository\ProductIndexerRepository;
use Domain\Product\Search\Indexer\Service\ProductIndexerInterface;

class ProductIndexer implements ProductIndexerInterface
{
    /**
     * @var ProductIndexerRepository
     */
    private $_searchRepository;

    public function __construct(){
        $this->_searchRepository = new ProductIndexerRepository();
    }

    /**
     * @param \Application_Model_Product $product
     * @param $visibilities
     * @param int $websiteId
     * @param array $children
     * @return bool
     */
    public function indexProduct( \Application_Model_Product $product, $visibilities, $websiteId = 2, $children = array())
    {
        return $this->_searchRepository->productIndexer($product, $visibilities, $websiteId, $children);
    }

    /**
     * @param string $productId
     * @param string $websiteId
     * @return bool
     */
    public function deleteProduct($productId, $websiteId ){
        return $this->_searchRepository->deleteProduct($productId, $websiteId);
    }

    /**
     * @param string $productId
     */
    public function sendQueue($productId){
        $this->_searchRepository->sendQueue($productId);
    }

    /**
     * @param string $index
     */
    public function setIndex($index = null){
        $this->_searchRepository->setIndex($index);
    }

    /**
     * @param $searchTerm
     * @param $totalHits
     * @param $storeId
     * @param $queryStart
     * @param $queryEnd
     * @return bool
     */
    public function indexQuery($searchTerm, $totalHits, $storeId, $queryStart, $queryEnd){
        return $this->_searchRepository->indexQuery($searchTerm, $totalHits, $storeId, $queryStart, $queryEnd);
    }
}
