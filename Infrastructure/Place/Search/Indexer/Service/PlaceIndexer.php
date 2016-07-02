<?php

namespace Infrastructure\Place\Search\Indexer\Service;

use Domain\Place\Search\Indexer\Service\PlaceIndexerInterface;
use Infrastructure\Place\Search\Indexer\Repository;

class PlaceIndexer implements PlaceIndexerInterface
{
    /**
     * @var \Infrastructure\Place\Search\Indexer\Repository\PlaceIndexerRepository
     */
    private $_searchRepository;

    public function __construct(){
        $this->_searchRepository = new Repository\PlaceIndexerRepository();
    }

    /**
     * @param \Application_Model_Place $place
     * @return bool
     */
    public function indexerPlace( \Application_Model_Place $place)
    {
        return $this->_searchRepository->index($place);
    }

    /**
     * @param $placeId
     * @return bool
     */
    public function deletePlace($placeId){
        return $this->_searchRepository->delete($placeId);
    }

    /**
     * @param $placeId
     */
    public function sendQueue($placeId){
        $this->_searchRepository->sendQueue($placeId);
    }

    /**
     * @param $indexName
     */
    public function setIndexName($indexName){
        $this->_searchRepository->setIndexName($indexName);
    }
}
