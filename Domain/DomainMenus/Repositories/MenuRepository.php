<?php

namespace Domain\DomainMenus\Repositories;

use Domain\DomainMenus\Objects\MainMenuCollection;

interface MenuRepository
{
    /**
     * Get the Main Menu adding the $webDomain to $menu->urlFinal attribute
     *
     * @param  string                  $webDomain
     * @return MainMenuCollection|null
     */
    public function getMainMenu($webDomain);

    /**
     * Get the Main Menu adding the $webDomain and $storeUrl
     * to $menu->urlFinal attribute that is needed
     *
     * @param  string                  $webDomain
     * @param  string                  $storeUrl
     * @return MainMenuCollection|null
     */
    public function getMainMenuByStore($webDomain, $storeUrl);
}
