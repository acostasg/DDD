<?php

namespace Domain\DomainWebSites\Objects;

use Domain\DomainShared\Objects\Id;

class WebSite
{
    /** @var  Id */
    private $id;
    /** @var  string */
    private $code;
    /** @var  string */
    private $locale;
    /** @var  string */
    private $name;
    /** @var  string */
    private $domain;

    /**
     * WebSite constructor.
     * @param Id $id
     * @param string $code
     * @param string $locale
     * @param string $name
     * @param string $domain
     */
    public function __construct(
        Id $id,
        $code,
        $locale,
        $name,
        $domain
    ) {
        $this->id = $id;
        $this->code = $code;
        $this->locale = $locale;
        $this->name = $name;
        $this->domain = $domain;
    }

    /**
     * @return Id
     */
    public function id()
    {
        return $this->id->id();
    }

    /**
     * @return string
     */
    public function code()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function locale()
    {
        return $this->locale;
    }


    public function name()
    {
        return $this->name;
    }

    public function domain()
    {
        return $this->domain;
    }
}
