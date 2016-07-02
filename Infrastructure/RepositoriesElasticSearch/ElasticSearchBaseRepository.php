<?php
/**
 * Created by PhpStorm.
 * User: albert
 * Date: 1/02/16
 * Time: 17:06
 */

namespace Infrastructure\RepositoriesElasticSearch;

use \Elastica\Client as Client;
use \Elastica\Query as Query;
use \Elastica\ResultSet as ResultSet;

class ElasticSearchBaseRepository
{
    /** @var array */
    private $servers = null;
    private $indexProduct = null;
    private $indexPlace = null;

    private static $client = null;

    /**
     * @param array $servers
     * @param string $indexProducts
     * @param string $indexPlaces
     * @throws \Exception
     */
    public function __construct($servers, $indexProducts, $indexPlaces)
    {
        if (empty($servers)) {
            throw new \Exception('I need a server for connect to elasticsearch');
        } elseif (empty($indexProducts)) {
            throw new \Exception('I need a index name product for connect to elasticsearch');
        }elseif(empty($indexPlaces)) {
            throw new \Exception('I need a index name place for connect to elasticsearch');
        }

        $this->servers = $servers;
        $this->indexProduct = $indexProducts;
        $this->indexPlace = $indexPlaces;
    }

    private function connect()
    {
        if (!self::$client) {
            self::$client = new Client($this->servers);
        }
        return self::$client;
    }

    /**
     * @param Query $queryElastica
     * @param string $websiteCode
     * @return ResultSet
     * @throws \Exception
     */
    protected function runQueryProduct(Query $queryElastica, $websiteCode = 'es')
    {
        return $this->connect()->getIndex($this->indexProduct)->getType('product_' . $websiteCode)->search($queryElastica);
    }

    /**
     * @param Query $queryElastica
     * @return ResultSet
     * @throws \Exception
     */
    protected function runQueryPlace(Query $queryElastica)
    {
        return $this->connect()->getIndex($this->indexPlace)->getType('places')->search($queryElastica);
    }
}