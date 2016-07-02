<?php

namespace Domain\DomainStores\Objects;

use Domain\DomainShared\Objects\Id;

class Store
{
    /** @var  Id */
    private $id;
    /** @var  string */
    private $name;

    public function __construct(
        Id $id,
        $name
    ) {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function id()
    {
        return $this->id->id();
    }

    /**
     * @return string
     */
    public function name()
    {
        return $this->name;
    }
}
