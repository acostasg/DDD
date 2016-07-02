<?php

namespace Infrastructure\RepositoriesCassandra;

use Application_Config_Loader as Config;

class CassandraEntityManager
{

    /**
     * Return the repository for get data
     *
     * @param $repositoryName The name of the repository to use.
     *                        Ex. 'Search\\CassandraCityRepository'
     * @return CassandraBaseRepository|null
     */
    public static function getRepository($repositoryName)
    {
        $repositoryClassName = 'Infrastructure\RepositoriesCassandra\\' . $repositoryName;
        if (class_exists($repositoryClassName)) {
            return new $repositoryClassName (
                new Config()
            );
        } else {
            return null;
        }
    }
}
