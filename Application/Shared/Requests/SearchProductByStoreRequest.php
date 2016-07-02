<?php

namespace Application\Shared\Requests;

use \Exception as Exception;

class SearchProductByStoreRequest
{
    /** @var  string  */
    private $codeWebSite;

    /** @var  string */
    private $idStore;

    /** @var array[string] */
    private $attributes;

    /** @var string */
    private $parentAttribute;

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
        $limitPage = null
    ) {

        if (empty($codeWebsite)) {
            throw new Exception('The $codeWebsite is required');
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
        $this->parentAttribute = $parentAttribute;
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