<?php

namespace Application\Shared\Responses;

use Domain\DomainCities\Objects\CityCollection;
use Domain\DomainMenus\Objects\MainMenuCollection;
use Domain\DomainWebSites\Objects\WebSite;

class GetHomeDataResponse
{

    /** @var  CityCollection */
    private $cities;
    /** @var  CityCollection */
    private $mainCities;
    /** @var  WebSite */
    private $website;
    /** @var  MainMenuCollection */
    private $mainMenu;

    /**
     * GetHomeDataResponse constructor.
     * @param CityCollection $cities
     * @param CityCollection $mainCities
     * @param WebSite $webSite
     * @param MainMenuCollection $mainMenuCollection
     */
    public function __construct(
        CityCollection $cities,
        CityCollection $mainCities,
        WebSite $webSite,
        MainMenuCollection $mainMenuCollection
    ) {
        $this->cities = $cities;
        $this->mainCities = $mainCities;
        $this->website = $webSite;
        $this->mainMenu = $mainMenuCollection;
    }

    /**
     * @return CityCollection
     */
    public function cities()
    {
        return $this->cities;
    }

    /**
     * @return CityCollection
     */
    public function mainCities()
    {
        return $this->mainCities;
    }

    /**
     * @return WebSite
     */
    public function website()
    {
        return $this->website;
    }

    /**
     * @return MainMenuCollection
     */
    public function mainMenu()
    {
        return $this->mainMenu;
    }
}
