<?php

namespace Domain\DomainCities\Objects;

class CityCollection implements \Iterator
{

    /** @var array */
    protected $collection = [];

    /** @var int */
    protected $position = 0;

    /**
     * Collection constructor.
     *
     */
    public function __construct()
    {
        $this->position = 0;
    }

    /**
     * @param City $object
     */
    public function add(City $object)
    {
        $tmpCollectionBefore = $this->collection;
        $tmpCollectionAfter = [];
        foreach ($this->collection as $key => $item)
        {
            if (0 < strcmp($item->name(), $object->name())) {
                if (0 < $key) {
                    $tmpCollectionBefore = array_slice(
                        $this->collection,
                        0,
                        $key
                    );
                } else {
                    $tmpCollectionBefore = [];
                }
                $tmpCollectionAfter = array_slice(
                    $this->collection,
                    $key
                );
                break;
            }
        }
        $tmpCollectionBefore[] = $object;
        $this->collection = array_merge($tmpCollectionBefore, $tmpCollectionAfter);
    }

    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @throws \Exception
     * @since 5.0.0
     */
    public function current()
    {
        if (isset($this->collection[$this->position])) {
            return $this->collection[$this->position];
        } else {
            /** @todo Improve this creating a custom exception */
            throw new \Exception('Not More Items');
        }
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid()
    {
        return isset($this->collection[$this->position]);
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        $this->position = 0;
    }
}
