<?php

namespace Domain\DomainMenus\Factories;

use Domain\DomainMenus\Objects\Menu;
use Domain\DomainMenus\Objects\MenuCollection;
use Domain\DomainShared\Objects\Id;

class MenuFactory
{
    /**
     * @param  string         $name
     * @param  string         $url
     * @param  string         $urlFinal
     * @param  int            $order
     * @param  string|null    $id
     * @param  int            $count
     * @param  MenuCollection $subMenus
     * @return Menu
     * @throws \Exception
     */
    public static function build(
        $name,
        $url,
        $urlFinal,
        $order = 0,
        $id = null,
        $count = 0,
        $subMenus = null
    ) {
        if (empty($name)) {
            throw new \Exception('Name is required to build a Menu Object');
        } elseif (empty($url)) {
            throw new \Exception('Url is required to build a Menu Object');
        } elseif (empty($urlFinal)) {
            throw new \Exception('UrlFinal is required to build a Menu Object');
        }

        $id = new Id($id);

        return new Menu(
            $id,
            $name,
            $url,
            $urlFinal,
            $order,
            $count,
            $subMenus
        );
    }
}
