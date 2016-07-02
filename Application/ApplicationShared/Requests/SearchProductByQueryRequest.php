<?php

namespace Application\ApplicationShared\Requests;

use \Exception as Exception;

/**
 * Created by PhpStorm.
 * User: albert
 * Date: 26/01/16
 * Time: 15:59
 */
class SearchProductByQueryRequest
{
    /** @var  string  */
    private $codeWebSite;

    /** @var  string */
    private $idStore;

    /** @var array[string] */
    private $attributes;

    /** @var string */
    private $parentAttribute;

    /** @var string */
    private $query;

    /** @var int */
    private $currentPage;

    /** @var int */
    private $limitPage;

    public function __construct(
        $codeWebsite,
        $idStore,
        $parentAttribute,
        $attributes = null,
        $currentPage = null,
        $limitPage = null,
        $query = null
    ) {

        if (empty($codeWebsite)) {
            throw new Exception('The codeWebsite is required');
        }

        if (empty($idStore)) {
            throw new Exception('The idStore is required');
        }

        if (empty($parentAttribute)) {
            throw new Exception('The parent attribute is required');
        }

        $this->currentPage = $currentPage;
        $this->limitPage = $limitPage;
        $this->codeWebSite = $codeWebsite;
        $this->idStore = $idStore;
        $this->attributes = $attributes;
        $this->parentAttribute = $attributes;
        $this->query = $query;
    }

    /**
     * @return string
     */
    public function idStore()
    {
        return $this->idStore;
    }

    /**
     * @return string
     */
    public function codeWebSite(){
        return $this->codeWebSite;
    }

    /**
     * @return array[string]|null
     */
    public function attributes()
    {
        return $this->attributes;
    }

    /**
     * @return String|null
     */
    public function query()
    {
        return $this->query;
    }

    public function parentAttribute()
    {
        return $this->parentAttribute;
    }

    public function currentPage()
    {
        return $this->currentPage;
    }

    public function limitPage(){
        return $this->limitPage;
    }
}