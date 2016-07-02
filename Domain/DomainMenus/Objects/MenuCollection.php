<?php

namespace Domain\DomainMenus\Objects;

class MenuCollection implements \Iterator
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
     * @param Menu $object
     */
    public function add(Menu $object)
    {
        $this->collection[] = $object;
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
