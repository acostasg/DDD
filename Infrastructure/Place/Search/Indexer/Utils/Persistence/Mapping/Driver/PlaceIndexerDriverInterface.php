<?php

namespace Infrastructure\Place\Search\Indexer\Utils\Persistence\Mapping\Driver;

use Elastica\Response;

interface PlaceIndexerDriverInterface
{

    /**
     * Return client search
     *
     * @return bool|\Elastica\Client
     */
    public static function get();

    /**
     * Makes calls to the elasticsearch server based on this index
     *
     * It's possible to make any REST query directly over this method
     *
     * @param  string                                   $path   Path to call
     * @param  string                                   $method Rest method to use (GET, POST, DELETE, PUT)
     * @param  array                                    $data   OPTIONAL Arguments as array
     * @param  array                                    $query  OPTIONAL Query params
     * @throws Exception\ConnectionException|\Exception
     * @return sring                                    Response
     */
    public static function request($path, $method = \Zend_Http_Client::GET, $data = array(), array $query = array());

    /**
     * Optimizes all search indices
     *
     * @param  array              $args OPTIONAL Optional arguments
     * @return \Elastica\Response Response object
     * @link http://www.elasticsearch.org/guide/reference/api/admin-indices-optimize.html
     */
    public static function optimizeAll($args = array());

    /**
     * Deletes documents with the given ids, index, type from the index
     *
     * @param  array                                $ids   Document ids
     * @param  string|\Elastica\Index               $index Index name
     * @param  string|\Elastica\Type                $type  Type of documents
     * @throws \Elastica\Exception\InvalidException
     * @return \Elastica\Bulk\ResponseSet           Response object
     */
    public static function deleteIds(array $ids, $index, $type);

    /**
     *  Injected/mock client elastic search
     *
     * @param $object
     * @throws \Elastica\Exception\InvalidException
     */
    public static function setClient($object);
}
