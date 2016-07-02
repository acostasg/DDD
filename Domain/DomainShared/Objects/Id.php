<?php

namespace Domain\DomainShared\Objects;

class Id
{
    /**
     * @var string $id
     */
    private $id;

    /**
     * Id constructor.
     * @param string $id
     */
    public function __construct($id = null)
    {
        if (empty($id)) {
            $id = uniqid();
        }
        $this->id = (string) $id;
    }

    /**
     * @return string
     */
    public function id()
    {
        return $this->id;
    }
}
