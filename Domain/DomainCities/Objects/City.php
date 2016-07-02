<?php

namespace Domain\DomainCities\Objects;

use Domain\DomainShared\Objects\Id;

class City
{
    /** @var  Id */
    private $id;
    /** @var  string */
    private $name;
    /** @var  string */
    private $url;
    /** @var  string */
    private $urlFinal;

    /**
     * City constructor.
     * @param Id $id
     * @param string $name
     * @param string $url
     * @param string $urlFinal
     */
    public function __construct(
        Id $id,
        $name,
        $url,
        $urlFinal
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->url = $url;
        $this->urlFinal = $urlFinal;
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

    /**
     * @return string
     */
    public function url()
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function urlFinal()
    {
        return $this->urlFinal;
    }
}
