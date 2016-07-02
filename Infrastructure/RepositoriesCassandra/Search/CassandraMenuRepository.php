<?php

namespace Infrastructure\RepositoriesCassandra\Search;

use Domain\DomainMenus\Exceptions\DuplicateItemException;
use Domain\DomainMenus\Factories\MenuFactory;
use Domain\DomainMenus\Objects\MainMenuCollection;
use Domain\DomainMenus\Repositories\MenuRepository;
use Infrastructure\RepositoriesCassandra\CassandraBaseRepository;

class CassandraMenuRepository extends CassandraBaseRepository implements MenuRepository
{

    /**
     * Get the Main Menu adding the $webDomain to $menu->urlFinal attribute
     *
     * @param string $webDomain
     * @return MainMenuCollection|null
     */
    public function getMainMenu($webDomain)
    {
        $listMenu = null;

        $idAttributesFamily = $this->getIdAttributes([
            'Family_mixed',
            'Family_National',
            'Family_Local',
            'Family'
        ]);
        $idMenuItems = $this->getMenuIdItems($idAttributesFamily);

        /** Get MenuItems using the Id of the menu returned by getMenuIdItems */
        $listDataTmpMenu = $this->getMenuItems(
            array_reduce(
                $idMenuItems,
                function ($idList, $item) {
                    $idList[] = $item['idMenu'];
                    return $idList;
                },
                []
            )
        );

        /** Add order to Items returned by GetMenuItems */
        $listDataTmpMenu = $this->addOrderToItemsFromListMenuItems(
            $listDataTmpMenu,
            $idMenuItems
        );
        /**  */

        $listMenu = new MainMenuCollection();

        foreach ($listDataTmpMenu as $item) {
            $menuItem = $this->buildMenuItem(
                $item['id'],
                $item['esTag'],
                $item['url'],
                $webDomain . $item['url'],
                $item['order']
            );
            $listMenu->add($menuItem);
        }

        return $listMenu;
    }

    /**
     * Get the Main Menu adding the $webDomain and $storeUrl
     * to $menu->urlFinal attribute that is needed
     *
     * @param string $webDomain
     * @param string $storeUrl
     * @return MainMenuCollection|null
     */
    public function getMainMenuByStore($webDomain, $storeUrl)
    {
        $listMenu = null;

        $idAttributesFamily = [
            'WithStore' => $this->getIdAttributes([
                                'Family_mixed',
                                'Family_Local',
                            ]),
            'WithoutStore' => $this->getIdAttributes([
                                'Family_National',
                                'Family'
                            ])
        ];

        $listMenu = new MainMenuCollection();

        foreach ($idAttributesFamily as $key => $attributeFamily) {
            $idMenuItems = $this->getMenuIdItems($attributeFamily);

            /** Get MenuItems using the Id of the menu returned by getMenuIdItems */
            $listDataTmpMenu = $this->getMenuItems(
                array_reduce(
                    $idMenuItems,
                    function ($idList, $item) {
                        $idList[] = $item['idMenu'];
                        return $idList;
                    },
                    []
                )
            );

            /** Add order to Items returned by GetMenuItems */
            $listDataTmpMenu = $this->addOrderToItemsFromListMenuItems(
                $listDataTmpMenu,
                $idMenuItems
            );
            /** */

            foreach ($listDataTmpMenu as $item) {
                $urlMenu = $webDomain . $item['url'];

                if ('WithStore' == $key) {
                    $urlMenu .= '/' . $storeUrl;
                }
                $menuItem = $this->buildMenuItem(
                    $item['id'],
                    $item['name'],
                    $item['url'],
                    $urlMenu,
                    $item['order']
                );
                try {
                    $listMenu->add($menuItem);
                } catch (DuplicateItemException $e) {
                    //Is not necessary add the item twice.
                }
            }
        }

        return $listMenu;
    }

    private function addOrderToItemsFromListMenuItems($menuItems, $orderMenuItems)
    {
        $orderMenuItems = array_reduce(
            $orderMenuItems,
            function ($orderItems, $item) {
                $orderItems[$item['idMenu']] = $item['order'];
                return $orderItems;
            },
            []
        );
        foreach ($menuItems as &$itemMenu) {
            if (array_key_exists($itemMenu['id'], $orderMenuItems)) {
                $itemMenu['order'] = $orderMenuItems[$itemMenu['id']];
            } else {
                $itemMenu['order'] = null;
            }
        }

        return $menuItems;
    }

    private function getIdAttributes($names)
    {
        $listAttributesId = [];
        $dataAttributes = $this->getMultipleItemsByRowKey(
            'ClassDirectory',
            $names
        );

        if (!empty($dataAttributes)) {
            foreach ($dataAttributes as $key => $attribute) {
                $listAttributesId[] = $attribute['classId'];
            }
        }

        return $listAttributesId;
    }

    private function getMenuIdItems($idAttributes)
    {
        $listAttributesId = [];
        $dataAttributes = $this->getMultipleItemsByRowKey(
            'RelClassAttributeDirectory',
            $idAttributes
        );

        if (!empty($dataAttributes)) {
            foreach ($dataAttributes as $attribute) {
                foreach ($attribute as $idMenu => $order) {
                    if (false === array_search($idMenu, array_column($listAttributesId, 'idMenu'))) {
                        $listAttributesId[] = [
                            'idMenu' => $idMenu,
                            'order' => $order
                        ];
                    }
                }
            }
        }

        return $listAttributesId;
    }

    private function getMenuItems($items)
    {
        $listMenuItems = [];
        $dataAttributes = $this->getMultipleItemsByRowKey(
            'AttributesDirectory',
            $items
        );

        if (!empty($dataAttributes)) {
            foreach ($dataAttributes as $id => $item) {
                if (isset($item['isUrl'])
                    && 1 == $item['isUrl']
                    && isset($item['isTag'])
                    && 1 == $item['isTag']
                    && $this->isOnTime(
                        (isset($item['initialDate'])?$item['initialDate']:null),
                        (isset($item['finalDate'])?$item['finalDate']:null)
                    )
                ) {
                    $tmpItem = [
                        'id' => $id,
                        'url' => $item['esUrl'],
                        'name' => $item['esTag'],
                    ];

                    $listMenuItems[] = $tmpItem;
                }
            }
        }

        return $listMenuItems;
    }

    private function isOnTime($startDate = null, $endDate = null)
    {
        $onTime = true;
        $now = time();
        if (!is_null($startDate)
            && $startDate > $now
        ) {
            $onTime = false;
        }
        if ($onTime
            && !is_null($endDate)
            && $endDate < $now
        ) {
            $onTime = false;
        }

        return $onTime;
    }

    private function buildMenuItem(
        $id,
        $name,
        $url,
        $urlFinal,
        $order
    ) {
        return MenuFactory::build(
            $name,
            $url,
            $urlFinal,
            $order,
            $id
        );
    }
}
