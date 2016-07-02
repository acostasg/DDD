<?php

namespace Infrastructure\Product\Search\Utils\Persistence\Mapping\Driver;

use Elastica\Response;

/**
 * Created by PhpStorm.
 * User: albert
 * Date: 24/12/15
 * Time: 10:54
 */
class ProductIndexerDriver implements ProductIndexerDriverInterface
{

    private static $_client = false;

    /**
     * Return client elastic search
     *
     * @return bool|\Elastica\Client
     */
    public static function get()
    {
        if (self::$_client) {
            return self::$_client;
        }
        $servers = \Application_Config_Loader::get('elasticsearch.servers');

        \Application_Monitor_Log::debug(__METHOD__ . " connecting to ElasticSearch", "servers", $servers);

        self::$_client = new \Elastica\Client(array('servers' => $servers));
        // Load client
        return self::$_client;
    }

    /**
     * Makes calls to the elasticsearch server based on this index
     *
     * It's possible to make any REST query directly over this method
     *
     * @param  string $path Path to call
     * @param  string $method Rest method to use (GET, POST, DELETE, PUT)
     * @param  array $data OPTIONAL Arguments as array
     * @param  array $query OPTIONAL Query params
     * @throws Exception\ConnectionException|\Exception
     * @return sring Response
     */
    public static function request($path, $method = Zend_Http_Client::GET, $data = array(), array $query = array())
    {

        if (!self::$_client) {
            self::get();
        }

        $elasticaResponse = self::$_client->request($path, $method, $data, $query);
        $response = $elasticaResponse->getData();

        if (!$elasticaResponse->isOk()) {
            throw new \Application_Model_Exception('Error call API elastic search',
                \Application_Model_Exception::ELASTIC_INVALID_RESPONSE, false,
                'response', $response
            );
        }

        if ($elasticaResponse->hasError()) {
            return 'KO: ' . $elasticaResponse->getError();
        }

        return 'OK: ' . $response['ok'] . ',  acknowledged:' . $response['acknowledged'];
    }

    /**
     * Optimizes all search indices
     *
     * @param  array $args OPTIONAL Optional arguments
     * @return \Elastica\Response Response object
     * @link http://www.elasticsearch.org/guide/reference/api/admin-indices-optimize.html
     */
    public static function optimizeAll($args = array())
    {
        return self::$_client->optimizeAll($args);
    }

    /**
     * Deletes documents with the given ids, index, type from the index
     *
     * @param  array $ids Document ids
     * @param  string|\Elastica\Index $index Index name
     * @param  string|\Elastica\Type $type Type of documents
     * @throws \Elastica\Exception\InvalidException
     * @return \Elastica\Bulk\ResponseSet                   Response object
     */
    public static function deleteIds(array $ids, $index, $type)
    {
        return self::$_client->deleteIds($ids, $index, $type);
    }

    /**
     *  Injected/mock client elastic search
     *
     * @param $object
     * @throws \Elastica\Exception\InvalidException
     */
    public static function setClient($object)
    {
        self::$_client = $object;
    }

}