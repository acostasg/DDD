<?php

namespace Domain\DomainMenus\Repositories;

use Domain\DomainMenus\Objects\MainMenuCollection;
use Domain\DomainProduct\Objects\Search\ProductByStore\AttributeSearchCountCollection;

interface SubMenuRepository
{
    /**
     * Get the Sub Menu by ID attribute Parent
     *
     * @param  string                         $idAttr
     * @param  AttributeSearchCountCollection $listCount
     * @return MainMenuCollection|null
     */
    public function getSubMenuByAttrParent($idAttr, AttributeSearchCountCollection $listCount);
}
