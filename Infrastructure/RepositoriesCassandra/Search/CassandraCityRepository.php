<?php

namespace Infrastructure\RepositoriesCassandra\Search;

use Domain\DomainCities\Factories\CityFactory;
use Domain\DomainCities\Objects\CityCollection;
use Domain\DomainCities\Repositories\CityRepository;
use Infrastructure\RepositoriesCassandra\CassandraBaseRepository;

class CassandraCityRepository extends CassandraBaseRepository implements CityRepository
{
    const WEB_SECURE_BASE_URL = 'web/secure/base_url';
    const WEB_UNSECURE_BASE_URL = 'web/unsecure/base_url';
    const ATTRIBUTES_DIRECTORY = 'AttributesDirectory';

    const SEPARATOR_CHARACTER = '|';
    const STORES = 'stores'.self::SEPARATOR_CHARACTER;
    const STORE = 'Store';
    const WEBSITE_ID = 'websiteId';
    const ID_FIELD = 'id';
    const ATTRIBUTE_ID_FIELD = 'attributeId';

    const CONF_PARAM = 'Conf';

    /**
     * Get all cities associate to the websiteId requested.
     *
     * @param $websiteId
     * @param bool $isHttps
     * @return CityCollection|null
     */
    public function getAllCitiesFromWebSite($websiteId, $isHttps = false)
    {
        $citiesList = null;

        $citiesDataList = $this->getCitiesDataFromStore($websiteId);

        $citiesAttributeIdList = [];
        $storesIdList = [];
        foreach ($citiesDataList as $city) {
            $citiesAttributeIdList[] = $city[self::ATTRIBUTE_ID_FIELD];
            $storesIdList[] = $city[self::ID_FIELD];
        }

        if (!empty($citiesAttributeIdList)) {
            $citiesData = $this->getMultipleItemsByRowKey(
                self::ATTRIBUTES_DIRECTORY,
                $citiesAttributeIdList
            );
            if ($isHttps) {
                $keyBaseUrl = self::WEB_SECURE_BASE_URL;
            } else {
                $keyBaseUrl = self::WEB_UNSECURE_BASE_URL;
            }
            $citiesStoreUrl = $this->getStoreUrlFromCitiesRowKey(
                $storesIdList,
                $keyBaseUrl
            );

            if (!empty($citiesData) && !empty($citiesStoreUrl)) {
                $citiesList = new CityCollection();
                foreach ($citiesDataList as $city) {
                    if (array_key_exists($city[self::ID_FIELD], $citiesStoreUrl)
                        && array_key_exists($city[self::ATTRIBUTE_ID_FIELD], $citiesData)
                    ) {
                        $citiesData[$city[self::ATTRIBUTE_ID_FIELD]]['urlFinal'] = $citiesStoreUrl[$city[self::ID_FIELD]];
                        try {
                            $city = $this->buildCityItem($city[self::ATTRIBUTE_ID_FIELD], $citiesData[$city[self::ATTRIBUTE_ID_FIELD]]);
                        } catch (\Exception $e) {
                            $city = null;
                        }

                        if (!is_null($city)) {
                            $citiesList->add($city);
                        }
                    }
                }
            }
        }

        return $citiesList;
    }

    private function getStoreUrlFromCitiesRowKey(
        $citiesStore,
        $path
    ) {
        $urls = [];
        $listKeys = [];
        foreach ($citiesStore as $cityId) {
            $listKeys[] = self::STORES .$cityId. self::SEPARATOR_CHARACTER .$path;
        }
        if (!empty($listKeys)) {
            $dataPaths = $this->getMultipleItemsByRowKey(
                self::CONF_PARAM,
                $listKeys
            );
        }
        if (!empty($dataPaths)) {
            foreach ($dataPaths as $path) {
                $urls[$path['scopeId']] = $path['value'];
            }
        }

        return $urls;
    }

    private function getCitiesDataFromStore($websiteId)
    {
        $citiesIdList = [];
        $citiesData = $this->getByRowIndexed(
            self::STORE,
            self::WEBSITE_ID,
            $websiteId
        );

        if (!empty($citiesData)) {
            foreach ($citiesData as $city) {
                if (isset($city[self::ATTRIBUTE_ID_FIELD])
                    && !empty($city[self::ATTRIBUTE_ID_FIELD])
                ) {
                    $citiesIdList[] = [
                        self::ID_FIELD => $city[self::ID_FIELD],
                        self::ATTRIBUTE_ID_FIELD => $city[self::ATTRIBUTE_ID_FIELD]
                    ];
                }
            }
        }

        return $citiesIdList;
    }

    private function buildCityItem($id, $data)
    {
        return CityFactory::build(
            $data['name'],
            $data['esUrl'],
            $data['urlFinal'],
            $id
        );
    }
}
