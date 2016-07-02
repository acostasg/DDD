<?php

namespace Infrastructure\Cache;

use Domain\Cache\CacheItemInterface;
use Domain\Cache\CacheItemPoolInterface;

class DummyCacheItemPool implements CacheItemPoolInterface
{
    public function getItem($key)
    {
        return null;
    }

    public function hasItem($key)
    {
        return false;
    }

    public function clear()
    {
        return true;
    }

    public function deleteItem($key)
    {
        return true;
    }

    public function save(CacheItemInterface $item)
    {
        return true;
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
