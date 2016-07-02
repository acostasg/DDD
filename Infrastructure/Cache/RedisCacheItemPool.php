<?php

namespace Infrastructure\Cache;

use Domain\Cache\CacheItemInterface;
use Domain\Cache\CacheItemPoolInterface;

class RedisCacheItemPool implements CacheItemPoolInterface
{
    private $redis;

    private $defaultTtl = false;

    public function __construct($ttl)
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

    public function save(CacheItemInterface $item)
    {
        return $this->redis->save($item->get(), $item->getKey(), array(), $this->defaultTtl);
    }

    public function getItems(array $keys = array())
    {
        // TODO: Implement getItems() method.
    }

    public function deleteItems(array $keys)
    {
        // TODO: Implement deleteItems() method.
    }

    public function saveDeferred(CacheItemInterface $item)
    {
        // TODO: Implement saveDeferred() method.
    }

    public function commit()
    {
        // TODO: Implement commit() method.
    }


}
