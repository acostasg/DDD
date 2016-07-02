<?php

namespace Domain\DomainStores\Factories;

use Domain\DomainShared\Objects\Id;
use Domain\DomainStores\Objects\Store;

class StoreFactory
{
    /**
     * @param  string      $name
     * @param  string|null $id
     * @return Store
     * @throws \Exception
     */
    public static function build(
        $name,
        $id = null
    ) {
        if (empty($name)) {
            throw new \Exception('Name of the store can not be empty');
        }

        $id = new Id($id);

        return new Store(
            $id,
            $name
        );
    }
}
