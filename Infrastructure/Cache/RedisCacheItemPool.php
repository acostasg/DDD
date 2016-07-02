<?php

namespace Infrastructure\Cache;

use Domain\Cache\CacheItemInterface;
use Domain\Cache\CacheItemPoolInterface;
use Domain\Cache\InvalidArgumentException;

class RedisCacheItemPool implements CacheItemPoolInterface
{
    private $redis;

    private $defaultTtl = false;

    function __construct($ttl)
    {
        $this->redis = new \Application_Cache_Backend_Redis();
        $this->defaultTtl = $ttl;
    }


    public function getItem($key)
    {
        return $this->redis->load($key);
    }

    public function hasItem($key)
    {
        return $this->redis->test($key);
    }

    public function clear()
    {
        return $this->redis->flushAll();
    }

    public function deleteItem($key)
    {
        return $this->redis->remove($key);
    }

    public function save($item, $key)
    {
        return $this->redis->save($item, $key, array(), $this->defaultTtl);
    }

}