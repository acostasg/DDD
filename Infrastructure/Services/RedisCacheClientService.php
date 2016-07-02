<?php

namespace Infrastructure\Services;

use Domain\DomainShared\Services\CacheClientInterface;
use \Predis\Client as RedisCli;

class RedisCacheClientService implements CacheClientInterface
{
    private $redisClient;

    public function __construct($hosts, $options)
    {
        $this->redisClient = new RedisCli(
            $hosts,
            $options
        );
    }

    /**
     * Return the content of the key requested
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get($key)
    {
//        $this->redisClient->connection();
        $data = $this->redisClient->get($key);

        $dataArray = json_decode($data);

        if (!is_null($dataArray)) {
            $data = $dataArray;
        }

        return $data;
    }

    /**
     * Save the data requested in the cache database.
     * You can indicate a ttl or set 0 to not expire the information
     *
     * @param string $key
     * @param string $value
     * @param int $ttl => ttl in seconds
     *
     * @return mixed
     */
    public function set($key, $value, $ttl = 0)
    {
        if (is_array($value)) {
            $value = json_encode($value);
        }

        $this->redisClient->set($key, $value);

        if (0 < $ttl) {
            $this->redisClient->expire($key, $ttl);
        }
    }

    /**
     * key to delete
     *
     * @param string $key
     *
     * @return mixed
     */
    public function del($key)
    {
        return $this->redisClient->del($key);
    }
}
