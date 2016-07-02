<?php

namespace Application\ApplicationShared\Commands\InternalCommands;

use Domain\DomainMenus\Repositories\MenuRepository;

class GetHomeMainMenuInternalCommand
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
     * @return \Domain\DomainMenus\Objects\MainMenuCollection|null
     */
    public function execute(
        $domain
    ) {
        return $this->menuRepository->getMainMenu($domain);
    }
}
