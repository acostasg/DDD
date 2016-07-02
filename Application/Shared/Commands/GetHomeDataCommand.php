<?php

namespace Application\Shared\Commands;

use Application\Shared\Commands\InternalCommands\GetMainMenuInternalCommand;
use Application\Shared\Requests\GetHomeDataRequest;
use Application\Shared\Responses\GetHomeDataResponse;
use Domain\DomainCities\Objects\CityCollection;
use Domain\DomainCities\Repositories\CityRepository;
use Domain\DomainMenus\Factories\MenuFactory;
use Domain\DomainMenus\Objects\MainMenuCollection;
use Domain\DomainMenus\Repositories\MenuRepository;
use Domain\DomainStores\Repositories\StoreRepository;
use Domain\DomainWebSites\Repositories\WebSiteRepository;

class GetHomeDataCommand
{
    /** @var array Cities to add to mainCities */
    private $mainCities = [
        'Barcelona',
        'Madrid',
        'Valencia'
    ];

    /** @var array url => name to replace to show on the view */
    private $mainMenuNameItems = [
        'restaurantes' => 'Restaurantes',
        'salud-belleza' => 'Salud y Belleza',
        'ocio' => 'Ocio',
        'cursos-formacion' => 'Cursos y Formación',
        'shopping' => 'Productos Exclusivos',
        'viajes-findesemana' => 'Viajes'
    ];

    /** @var CityRepository  */
    private $cityRepository;
    /** @var StoreRepository  */
    private $storeRepository;
    /** @var  WebSiteRepository */
    private $websiteRepository;
    /** @var  MenuRepository */
    private $menuRepository;

    public function __construct(
        CityRepository $cityRepository,
        StoreRepository $storeRepository,
        WebSiteRepository $websiteRepository,
        MenuRepository $menuRepository
    ) {
        $this->cityRepository = $cityRepository;
        $this->storeRepository = $storeRepository;
        $this->websiteRepository = $websiteRepository;
        $this->menuRepository = $menuRepository;
    }

    /**
     * @param  GetHomeDataRequest  $request
     * @return GetHomeDataResponse
     */
    public function execute(GetHomeDataRequest $request)
    {
        $website = $this->websiteRepository->getWebSiteByCode(
            $request->websiteCode(),
            $request->isHttps()
        );
        $mainMenu = $this->getMainMenu($website->domain());
        $citiesCollection = $this->cityRepository->getAllCitiesFromWebSite(
            $website->id(),
            $request->isHttps()
        );
        $mainCityCollection = $this->getMainCities($citiesCollection);

        return new GetHomeDataResponse(
            $citiesCollection,
            $mainCityCollection,
            $website,
            $mainMenu
        );
    }

    private function getMainCities(CityCollection $citiesCollection)
    {
        $mainCityCollection = null;

        if (!empty($citiesCollection)) {
            $mainCityCollection = new CityCollection();
            foreach ($this->mainCities as $mainCity) {
                foreach ($citiesCollection as $city) {
                    if ($city->name() == $mainCity) {
                        $mainCityCollection->add($city);
                        break;
                    }
                }
            }
        }

        return $mainCityCollection;
    }

    private function getMainMenu($domain)
    {
        $cmd = new GetMainMenuInternalCommand(
            $this->menuRepository
        );

        $mainMenuCollection = $cmd->execute($domain);

        if (!empty($mainMenuCollection)) {
            $mainMenuCollection = $this->setCorrectMenuNames($mainMenuCollection);
        }

        return $mainMenuCollection;
    }

    private function setCorrectMenuNames(MainMenuCollection $mainMenuCollection)
    {
        $menuCollection = new MainMenuCollection();

        foreach ($mainMenuCollection as $menuItem) {
            $menuIndex = $menuItem->url();
            if (array_key_exists($menuIndex, $this->mainMenuNameItems)) {
                $menuItem = MenuFactory::build(
                    $this->mainMenuNameItems[$menuIndex],
                    $menuItem->url(),
                    $menuItem->urlFinal(),
                    $menuItem->order(),
                    $menuItem->id()
                );
            }
            $menuCollection->add($menuItem);
        }

        return $menuCollection;
    }
}
