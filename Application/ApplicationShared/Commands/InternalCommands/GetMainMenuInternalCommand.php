<?php

namespace Application\ApplicationShared\Commands\InternalCommands;

use Domain\DomainMenus\Repositories\MenuRepository;

class GetMainMenuInternalCommand
{
    /** @var MenuRepository  */
    private $menuRepository;

    /**
     * GetMainMenuInternalCommand constructor.
     * @param MenuRepository $menuRepository
     */
    public function __construct(MenuRepository $menuRepository)
    {
        $this->menuRepository = $menuRepository;
    }

    /**
     * @param string $domain
     * @param string|null $storeUrl
     * @return \Domain\DomainMenus\Objects\MainMenuCollection|null
     */
    public function execute(
        $domain,
        $storeUrl = null
    ) {
        if (!empty($store)) {
            return $this->menuRepository->getMainMenuByStore($domain, $storeUrl);
        } else {
            return $this->menuRepository->getMainMenu($domain);
        }
    }
}
