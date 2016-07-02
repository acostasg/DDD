<?php

namespace Infrastructure\Product\Search\Indexer\Repository;

use Infrastructure\Product\Search\Utils\ProductIndexerMapping;
use Infrastructure\Product\Search\Utils\Persistence\Mapping\Driver\ProductIndexerDriver;

use \Elastica\Query;
use \Elastica\Filter;
use \Elastica\Document;

class ProductIndexerRepository
{

    private static $_websiteActives;
    protected $_index;

    public function __construct(){
        $this->_index = \Application_Config_Loader::get('elasticsearch.index');
    }

    /**
     * Created Queue for index product
     * Used log for save hash queue
     * @param string $productId
     * @return void
     */
    public function sendQueue($productId)
    {
        try {
            //Queueu for index Elasticsearch
            $job = new \Application_Model_Job();
            $job->jobApiName = 'product';
            $job->jobMethodName = 'indexProductById';
            $job->jobOptions = $productId;
            $job->adminUserId = (\Zend_Session::getId()) ? \Zend_Session::getId() : 'notSessionId';
            $job->description = "Elastic Search product indexed with ID::" . $productId;
            $hash = \Application_Helper_JobFactory::create(array($job), $job->adminUserId);
            \Application_Monitor_Log::info(__METHOD__, ' Create queue index product elasticseach ', 'productId',
                $productId, 'hash', $hash);
        } catch (\Exception $e) {
            \Application_Monitor_Log::error(__METHOD__, ' Error to index elasticseach product ', 'productId', $productId,
                'error', $e->getMessage());
        }
    }

    //Forced index
    public function setIndex($index = null)
    {
        if (!is_null($index)){
            $this->_index = $index;
        }
    }

    /**
     *
     *  Index Product direct in the elasticsearch
     *
     * IMPORTANT used sendQueue for index asynchronous
     *
     * @param $product \Application_Model_Product
     * @param array $visibilities array of Application_Model_ProductStoreVisibility
     * @param integer $websiteId
     * @param array $children optional array of Application_Model_Product
     * @return bool
     */
    public function productIndexer($product, $visibilities, $websiteId = 2, $children = array())
    {
        \Application_Monitor_Log::info(__METHOD__ . ' Index product to elasticSearch ', 'productId', $product->id);

        $activeWebsites = $this->_getWebsitesActives();
        if (!array_key_exists($websiteId, $activeWebsites)) {
            \Application_Monitor_Log::error(__METHOD__, ' product no indexed ' . $product->id . ' website not active ',
                $websiteId);
            return 3;
        }

        $website = $activeWebsites[$websiteId];
        $document = ProductIndexerMapping::modelToDocument($product, $visibilities, $website, $children);
        $elasticDocument = new Document($product->id, $document);
        $elasticType = ProductIndexerDriver::get()->getIndex($this->_index)->getType("product_$website->code");

        \Application_Monitor_Log::info(__METHOD__, 'elasticType', $elasticType);

        $response = $elasticType->addDocument($elasticDocument);

        if ($response->hasError()) {
            \Application_Monitor_Log::error(__METHOD__, 'ElasticSearch Error', $response->getError());
        }

        // Refresh Index
        $elasticType->getIndex()->refresh();

        return $response->isOk();
    }

    /**
     * @param string $productId
     * @param integer $websiteId
     * @return bool
     */
    public function deleteProduct($productId, $websiteId = 2)
    {
        \Application_Monitor_Log::info(__METHOD__ . ' delete product in elasticSearch ', '$product', $productId,
            '$websiteId', $websiteId);

        $activeWebsites = $this->_getWebsitesActives();
        if (!array_key_exists($websiteId, $activeWebsites)) {
            \Application_Monitor_Log::error(__METHOD__, ' product no deleted ' . $productId . ' website not active ',
                $websiteId);
            return 3;
        }

        try {
            $website = $activeWebsites[$websiteId];
            $elasticType =  ProductIndexerDriver::get()->getIndex($this->_index)->getType("product_$website->code");
            $response = $elasticType->deleteById($productId);
        } catch (\Exception $e) {
            \Application_Monitor_Log::error(__METHOD__, ' product no deleted ' . $productId . ' error ', $e);
            return false;
        }

        if ($response->hasError()) {
            \Application_Monitor_Log::error(__METHOD__, 'ElasticSearch Error', $response->getError());
        }

        // Refresh Index
        $elasticType->getIndex()->refresh();

        return $response->isOk();
    }

    /**
     * Indexed query from users search in the front box
     *
     * @param $searchTerm
     * @param $totalHits
     * @param $storeId
     * @param $queryStart
     * @param $queryEnd
     * @return bool
     */
    public function indexQuery($searchTerm, $totalHits, $storeId, $queryStart, $queryEnd)
    {
        $store = $this->_getStore($storeId);
        $document = array(
            'query' => $searchTerm,
            'totalHits' => $totalHits,
            'date' => date('c'),
            'storeCode' => $store->code,
            'storeId' => $storeId,
            'storeName' => $store->name,
            'websiteId' => $store->websiteId,
            'queryStart' => $queryStart,
            'queryEnd' => $queryEnd,
            'location' => array(trim($store->longitude, '[]'), trim($store->latitude, '[]'))
        );
        \Application_Monitor_Log::info(__METHOD__, 'Executed query', $document);
        $elasticaDocument = new Document('', $document);
        $elasticaType = ProductIndexerDriver::get()->getIndex($this->_index)->getType('query');
        $response = $elasticaType->addDocument($elasticaDocument);
        if ($response->hasError()) {
            \Application_Monitor_Log::error(__METHOD__, 'ElasticSearch Error', $response->getError());
        }
        // Refresh Index
        $elasticaType->getIndex()->refresh();

        return $response->isOk();
    }


    /**
     * @return mixed
     */
    protected function _getWebsitesActives()
    {

        if (is_null(static::$_websiteActives)) {
            $WebSites = $this->_getApi('config')->cache(\Application_Cache_TTL::HIGH)->getWebsites();
            foreach ($WebSites as $website) {
                static::$_websiteActives[$website->id] = $website;
            }
        }
        return static::$_websiteActives;
    }

    /**
     * Uses Application_Helper_ExternalApi extApi to get an Application_Rpc_Client_Fa
     * @param string $api name {order, member, provider...}
     */
    protected function _getApi($api)
    {
        return \Application_Helper_ExternalApi::factory()->getApi($api);
    }

    protected function _getStore($storeId)
    {
        return $this->_getApi('config')->cache(\Application_Cache_TTL::HIGH)->getStoreById($storeId);
    }

}
