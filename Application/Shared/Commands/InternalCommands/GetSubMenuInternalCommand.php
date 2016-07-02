<?php

namespace Application\Shared\Commands\InternalCommands;

use Domain\DomainMenus\Repositories\SubMenuRepository;
use Domain\DomainProduct\Objects\Search\ProductByStore\AttributeSearchCountCollection;
use Infrastructure\RepositoriesCassandra\CassandraBaseRepository;

class GetSubMenuInternalCommand
{
    /** @var SubMenuRepository $subMenuRepository  */
    private $subMenuRepository;

    /**
     * GetMainMenuInternalCommand constructor.
     * @param CassandraBaseRepository $subMenuRepository
     */
    public function __construct(CassandraBaseRepository $subMenuRepository)
    {
        $this->subMenuRepository = $subMenuRepository;
    }

    /**
     * @param string $attrParent
     * @param AttributeSearchCountCollection $listCount
     * @return \Domain\DomainMenus\Objects\MainMenuCollection|null
     */
    public function execute(
        $attrParent,
        AttributeSearchCountCollection $listCount
    ) {
        return $this->subMenuRepository->getSubMenuByAttrParent($attrParent, $listCount);
    }
}
