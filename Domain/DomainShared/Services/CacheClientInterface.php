<?php

namespace Domain\DomainShared\Services;

interface CacheClientInterface
{
    /**
     * Return the content of the key requested
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get($key);

    /**
     * Save the data requested in the cache database.
     * You can indicate a ttl or set 0 to not expire the information
     *
     * @param string $key
     * @param string $value
     * @param int    $ttl   => ttl in seconds
     *
     * @return mixed
     */
    public function set($key, $value, $ttl = 0);

    /**
     * key to delete
     *
     * @param string $key
     *
     * @return mixed
     */
    public function del($key);
}
