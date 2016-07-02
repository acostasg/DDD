<?php

namespace Domain\DomainMenus\Objects;

use Domain\DomainShared\Objects\Id;

class Menu
{
    /** @var  Id */
    private $id;
    /** @var  string */
    private $name;
    /** @var  string */
    private $url;
    /** @var  string */
    private $urlFinal;
    /** @var  int */
    private $order;
    /** @var  int */
    private $count;
    /** @var  MenuCollection */
    private $subMenus;

    /**
     * Menu constructor.
     * @param Id             $id
     * @param string         $name
     * @param string         $url
     * @param string         $urlFinal
     * @param int            $order
     * @param int            $count
     * @param MenuCollection $subMenus
     */
    public function __construct(
        Id $id,
        $name,
        $url,
        $urlFinal,
        $order = 0,
        $count = 0,
        $subMenus = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->url = $url;
        $this->urlFinal = $urlFinal;
        $this->order = $order;
        $this->count = $count;
        $this->subMenus = $subMenus;
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

    /**
     * @return int
     */
    public function order()
    {
        return $this->order;
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->count;
    }

    /**
     * @return MenuCollection
     */
    public function subMenus()
    {
        return $this->subMenus;
    }
}
