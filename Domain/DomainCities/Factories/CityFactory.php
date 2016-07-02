<?php

namespace Domain\DomainCities\Factories;

use Domain\DomainCities\Objects\City;
use Domain\DomainShared\Objects\Id;

class CityFactory
{
    /**
     * @param string $name
     * @param string $url
     * @param string $urlFinal
     * @param string|null $id
     * @return City
     * @throws \Exception
     */
    public static function build(
        $name,
        $url,
        $urlFinal,
        $id = null
    ) {
        if (empty($name)) {
            throw new \Exception('Name of the city can not be empty');
        } elseif (empty($url)) {
            throw new \Exception('Url of the city can not be empty');
        } elseif (empty($urlFinal)) {
            throw new \Exception('UrlFinal of the city can not be empty');
        }

        $id = new Id($id);

        return new City(
            $id,
            $name,
            $url,
            $urlFinal
        );
    }
}
