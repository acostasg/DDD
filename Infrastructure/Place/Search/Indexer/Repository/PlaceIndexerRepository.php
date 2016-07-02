<?php

namespace Infrastructure\Place\Search\Indexer\Repository;

use Elastica\Document;
use Infrastructure\Place\Search\Indexer\Utils\Persistence\Mapping\Driver\PlaceIndexerDriver;

class PlaceIndexerRepository
{

    private $_indexName = null;

    /**
     * Created Queue for index product
     * Used log for save hash queue
     * @param string $id
     * @return void
     */
    public function sendQueue($id)
    {
        try {
            //Queueu for index Elasticsearch
            $job = new \Application_Model_Job();
            $job->jobApiName = 'provider';
            $job->jobMethodName = 'indexPartnerById';
            $job->jobOptions = $id;
            $job->adminUserId = (\Zend_Session::getId()) ? \Zend_Session::getId() : 'notSessionId';
            $job->description = "Elastic Search product indexed with ID::" . $id;
            $hash = \Application_Helper_JobFactory::create(array($job), $job->adminUserId);
            \Application_Monitor_Log::info(__METHOD__, ' Create queue index partner elasticseach ', 'partnerId',
                $id, 'hash', $hash);
        } catch (\Exception $e) {
            \Application_Monitor_Log::error(__METHOD__, ' Error to index elasticseach partner', 'partnerId', $id,
                'error', $e->getMessage());
        }
    }

    public function setIndexName($name){
        $this->_indexName = $name;
        return $this;
    }

    /**
     *
     *  Index Place direct in the elasticsearch
     *
     * IMPORTANT used sendQueue for index asynchronous
     *
     * @param $place \Application_Model_Place
     * @return bool
     */
    public function index( \Application_Model_Place $place)
    {
        \Application_Monitor_Log::info(__METHOD__ . ' Index partner to elasticSearch ', 'place', $place->id);

        $document = $place->toArray();

        //config
        $type = \Application_Config_Loader::get('elasticsearch.partners.type');

        //geopoints
        $document['location']['lat'] = ($place->latitudeGeo)?$place->latitudeGeo:0;
        $document['location']['lon'] = ($place->longitudeGeo)?$place->longitudeGeo:0;
        $place->encode();
        $document['mediaGallery'] = $place->getIteratorMediaGallery();
        $review = $place->getReview();
        $document['ranking'] = (empty($review['ranking']))?0:(integer)$review['ranking'];

        $elasticaDocument = new Document($place->id, $document);

        $elasticaType =  PlaceIndexerDriver::get()->getIndex($this->getIndexName())->getType($type);

        $response = $elasticaType->addDocument($elasticaDocument);

        if ($response->hasError()) {
            \Application_Monitor_Log::error(__METHOD__, 'ElasticSearch Error', $response->getError());
        }

        // Refresh Index
        $elasticaType->getIndex()->refresh();

        return $response->isOk();
    }

    /**
     * @param integer $placeId
     * @return bool
     */
    public function delete($placeId)
    {
        \Application_Monitor_Log::info(__METHOD__ . ' delete partner in elasticSearch ', 'placeId', $placeId);

        try {
            //config
            $type = \Application_Config_Loader::get('elasticsearch.partners.type');
            $elasticaType = PlaceIndexerDriver::get()->getIndex($this->getIndexName())->getType($type);
            $response = $elasticaType->deleteById($placeId);
        } catch (\Exception $e) {
            \Application_Monitor_Log::error(__METHOD__, ' place no deleted ' . $placeId . ' error ', $e);
            return false;
        }

        if ($response->hasError()) {
            \Application_Monitor_Log::error(__METHOD__, 'ElasticSearch Error', $response->getError());
        }

        // Refresh Index
        $elasticaType->getIndex()->refresh();

        return $response->isOk();
    }

    /**
     * @return string
     */
    public function getIndexName(){
        if(is_null($this->_indexName)){
            $this->_indexName = \Application_Config_Loader::get('elasticsearch.partners.index');
        }
        return $this->_indexName;
    }

}
