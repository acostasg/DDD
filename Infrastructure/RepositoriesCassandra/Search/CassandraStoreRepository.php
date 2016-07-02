<?php

namespace Infrastructure\RepositoriesCassandra\Search;

use Domain\DomainStores\Factories\StoreFactory;
use Domain\DomainStores\Repositories\StoreRepository;
use Infrastructure\RepositoriesCassandra\CassandraBaseRepository;

class CassandraStoreRepository extends CassandraBaseRepository implements StoreRepository
{
    /**
     * Get the Store Data identified by storeName [santacruztenerife]
     * and websiteCode [es, mp, ...]
     *
     * @param $storeName
     * @param $websiteCode
     * @return \Domain\DomainStores\Objects\Store|null
     */
    public function getStoreFromName($storeName, $websiteCode)
    {
        $store = null;
        $code = $storeName . '_' . $websiteCode;
        $storeData = $this->getByRowIndexed(
            'Store',
            'code',
            $code,
            '',
            1
        );

        if (!empty($storeData)) {
            foreach ($storeData as $key => $columns) {
                try {
                    $store = $this->buildStoreItem($columns);
                } catch (\Exception $e) {
                    $store = null;
                }
            }
        }

        return $store;
    }

    /**
     * Get the default Store Data
     *
     * @return Store
     */
    public function getDefaultStore()
    {
        $store = null;
        $storeData = $this->getByRowIndexed(
            'Store',
            'code',
            'default_es',
            '',
            1
        );

        if (!empty($storeData)) {
            foreach ($storeData as $key => $columns) {
                try {
                    $store = $this->buildStoreItem($columns);
                } catch (\Exception $e) {
                    $store = null;
                }
            }
        }

        return $store;
    }

    private function buildStoreItem($data)
    {
        return StoreFactory::build(
            $data['name'],
            $data['id']
        );
    }
}
