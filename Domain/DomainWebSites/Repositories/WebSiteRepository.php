<?php

namespace Domain\DomainWebSites\Repositories;

use Domain\DomainWebSites\Objects\WebSite;

interface WebSiteRepository
{
    /**
     * Get the WebSite data Identified by code [es, pl, ...]
     *
     * @param  string  $code
     * @param  bool    $isHttps
     * @return WebSite
     */
    public function getWebSiteByCode($code, $isHttps = false);
}
