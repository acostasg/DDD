<?php

namespace Infrastructure\RepositoriesCassandra;

use Application_Config_Loader as Config;
use cassandra\NotFoundException;
use phpcassa\Connection\ConnectionPool;
use phpcassa\ColumnFamily;
use phpcassa\Index\IndexClause;
use phpcassa\Index\IndexExpression;

class CassandraBaseRepository
{
    const CASSANDRA_KEYSPACE = "cassandra.keyspace";
    const CASSANDRA_SERVERS = "cassandra.servers";
    
    /** @var  array */
    private $poolServers;
    /** @var  string */
    private $keySpace;
    /** @var  ConnectionPool */
    private $connection;

    /**
     * CassandraBaseRepository constructor.
     * @param Config $configService
     * @throws \Exception
     */
    public function __construct(Config $configService)
    {
        $keySpace = $configService->get(self::CASSANDRA_KEYSPACE);
        $servers = $configService->get(self::CASSANDRA_SERVERS);

        if (empty($servers)) {
            throw new \Exception('I need a server for connect to cassandra');
        } elseif (empty($keySpace)) {
            throw new \Exception('I need a keyspace for connect to cassandra');
        }

        $serverListFinal = [];
        foreach ($servers as $server) {
            if (isset($server['host']) && isset($server['port'])) {
                $serverListFinal[] = $server['host'] . ':' . $server['port'];
            }
        }

        if (empty($serverListFinal)) {
            throw new \Exception(
                'Probably the format of hosts for cassandra are wrong. Please review the configuration ' .
                'local.json file.'
            );
        }

        $this->keySpace = $keySpace;
        $this->poolServers = $serverListFinal;
    }

    /**
     * Open the connection to Cassandra DB
     * @return ConnectionPool
     */
    protected function connect()
    {
        if (empty($this->connection)) {
            $this->connection = new ConnectionPool($this->keySpace, $this->poolServers);
        }
        return $this->connection;
    }

    /**
     * Get data filtering by row indexed.
     * Use $startAt and $limit for limit the results. By default is at 100 items
     * Put $startAt to an empty string to start at beginning
     * @see phpcassa\Index\IndexClause
     *
     * @param string $columnFamily
     * @param string $rowIndexedName
     * @param string $rowIndexedValue
     * @param string $startAt Index to start. Empty to start at the beginning
     * @param int $limit
     * @return array|null
     *
     */
    protected function getByRowIndexed(
        $columnFamily,
        $rowIndexedName,
        $rowIndexedValue,
        $startAt = '',
        $limit = 0
    ) {
        $connection = $this->connect();
        $em = new ColumnFamily($connection, $columnFamily);

        $indexExp = new IndexExpression($rowIndexedName, $rowIndexedValue);
        if (0 < $limit) {
            $indexClause = new IndexClause([$indexExp], $startAt, $limit);
        } else {
            $indexClause = new IndexClause([$indexExp]);
        }
        try {
            $result = $em->get_indexed_slices($indexClause);
        } catch (NotFoundException $e) {
            $result = null;
        }

        return $result;
    }

    /**
     * Get multiple items filtering by identifier row index.
     *
     * @param string $columnFamily
     * @param array $listKeys array with all index to get
     * @return array|null
     */
    protected function getMultipleItemsByRowKey(
        $columnFamily,
        $listKeys
    ) {
        $connection = $this->connect();
        $em = new ColumnFamily($connection, $columnFamily);

        try {
            $result = $em->multiget($listKeys);
        } catch (NotFoundException $e) {
            $result = null;
        }

        return $result;
    }

    /**
     * Get one item filtering by identifier row index.
     *
     * @param string $columnFamily
     * @param string $key
     * @return array|null
     */
    protected function getOneItemsByRowKey(
        $columnFamily,
        $key
    ) {
        $connection = $this->connect();
        $em = new ColumnFamily($connection, $columnFamily);

        try {
            $result = $em->get($key);
        } catch (NotFoundException $e) {
            $result = null;
        }

        return $result;
    }
}
