<?php

namespace Infrastructure\Cache;

use Domain\Cache\CacheItemInterface;
use Domain\Cache\CacheItemPoolInterface;
use Domain\Cache\InvalidArgumentException;

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

    public function save($item, $key)
    {
        return true;
    }

}