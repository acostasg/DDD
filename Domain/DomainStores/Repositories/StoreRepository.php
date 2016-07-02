<?php

namespace Domain\DomainStores\Repositories;

use Domain\DomainStores\Objects\Store;

interface StoreRepository
{
    /**
     * Get the default Store Data
     *
     * @return Store
     */
    public function getDefaultStore();

    /**
     * Get the Store Data identified by storeName [santacruztenerife]
     * and websiteCode [es, mp, ...]
     *
     * @param $slug
     * @return Store
     */
    public function getStoreFromName($slug, $websiteCode);
}
