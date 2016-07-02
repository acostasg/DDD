<?php

namespace Infrastructure\RepositoriesElasticSearch;

use Application_Config_Loader as Config;

class ElasticSearchEntityManager
{

    const ELASTICSEARCH_INDEX = "elasticsearch.index";
    const ELASTICSEARCH_PARTNERS_INDEX = "elasticsearch.partners.index";
    const ELASTICSEARCH_SERVERS = "elasticsearch.servers";

    public static function getRepository($repositoryName)
    {
        $repositoryCompleteName = 'Infrastructure\RepositoriesElasticSearch\\' . $repositoryName;
        if (class_exists($repositoryCompleteName)) {
            return new $repositoryCompleteName (
                array('servers' => Config::get(self::ELASTICSEARCH_SERVERS)),
                Config::get(self::ELASTICSEARCH_INDEX),
                Config::get(self::ELASTICSEARCH_PARTNERS_INDEX)
            );
        } else {
            return null;
        }
    }
}
