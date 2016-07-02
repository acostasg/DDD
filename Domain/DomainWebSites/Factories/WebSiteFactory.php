<?php

namespace Domain\DomainWebSites\Factories;

use Domain\DomainShared\Objects\Id;
use Domain\DomainWebSites\Objects\WebSite;

class WebSiteFactory
{
    /**
     * @param  string      $code
     * @param  string      $locale
     * @param  string      $name
     * @param  string      $domain
     * @param  string|null $id
     * @return WebSite
     * @throws \Exception
     */
    public static function build(
        $code,
        $locale,
        $name,
        $domain,
        $id = null
    ) {
        if (empty($code)) {
            throw new \Exception('Code of website object can not be empty');
        } elseif (empty($locale)) {
            throw new \Exception('Locale of website object can not be empty');
        } elseif (empty($name)) {
            throw new \Exception('Name of website object can not be empty');
        } elseif (empty($domain)) {
            throw new \Exception('Domain of website object can not be empty');
        }

        $id = new Id($id);

        return new WebSite(
            $id,
            $code,
            $locale,
            $name,
            $domain
        );
    }
}
