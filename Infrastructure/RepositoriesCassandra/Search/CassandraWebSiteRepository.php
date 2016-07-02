<?php

namespace Infrastructure\RepositoriesCassandra\Search;

use Domain\DomainWebSites\Factories\WebSiteFactory;
use Domain\DomainWebSites\Objects\WebSite;
use Domain\DomainWebSites\Repositories\WebSiteRepository;
use Infrastructure\RepositoriesCassandra\CassandraBaseRepository;

class CassandraWebSiteRepository extends CassandraBaseRepository implements WebSiteRepository
{
    /**
     * Get the WebSite data Identified by code [es, pl, ...]
     *
     * @param string $code
     * @param bool $isHttps
     * @return WebSite
     */
    public function getWebSiteByCode($code, $isHttps = false)
    {
        $website = null;
        $websData = $this->getByRowIndexed(
            'Website',
            'code',
            $code,
            '',
            1
        );

        if (!empty($websData)) {
            foreach ($websData as $key => $columns) {
                $columns['domain'] = $this->getDomain($columns['id'], $isHttps);
                try {
                    $website = $this->buildWebItem($columns);
                } catch (\Exception $e) {
                    $website = null;
                }
            }
        }

        return $website;
    }

    private function getDomain($idWebSite, $isHttps = false)
    {
        $domain = null;

        if (!is_null($idWebSite)) {
            if ($isHttps) {
                $key = 'websites|'.$idWebSite.'|web/secure/base_url';
            } else {
                $key = 'websites|'.$idWebSite.'|web/unsecure/base_url';
            }
            $dataDomain = $this->getOneItemsByRowKey(
                'Conf',
                $key
            );

            if (!empty($dataDomain)) {
                $domain = $dataDomain['value'];
            }
        }

        return $domain;
    }

    private function buildWebItem($data)
    {
        return WebSiteFactory::build(
            $data['code'],
            $data['locale'],
            $data['name'],
            $data['domain'],
            $data['id']
        );
    }
}
