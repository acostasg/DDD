<?php

namespace Application\Shared\Requests;

class GetHomeDataRequest
{
    /** @var  bool */
    private $isHttps;
    /** @var string  */
    private $store;
    /** @var  string */
    private $websiteCode;

    /**
     * GetHomeDataRequest constructor.
     * @param string $websiteCode
     * @param string|null $store
     * @param bool $isHttps
     */
    public function __construct(
        $websiteCode,
        $store = null,
        $isHttps = false
    ) {
        $this->websiteCode = $websiteCode;
        $this->store = $store;
        $this->isHttps = $isHttps;
    }

    /**
     * @return string
     */
    public function websiteCode()
    {
        return $this->websiteCode;
    }

    /**
     * @return null|string
     */
    public function store()
    {
        return $this->store;
    }

    public function isHttps()
    {
        return $this->isHttps;
    }
}
