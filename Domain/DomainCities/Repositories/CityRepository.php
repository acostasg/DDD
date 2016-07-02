<?php

namespace Domain\DomainCities\Repositories;

use Domain\DomainCities\Objects\CityCollection;

interface CityRepository
{
    /**
     * Get all cities associate to the websiteId requested.
     *
     * @param $websiteId
     * @param  bool                $isHttps
     * @return CityCollection|null
     */
    public function getAllCitiesFromWebSite($websiteId, $isHttps = false);
}
