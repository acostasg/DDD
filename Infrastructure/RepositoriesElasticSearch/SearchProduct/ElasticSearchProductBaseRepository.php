<?php

namespace Infrastructure\RepositoriesElasticSearch\SearchProduct;

use Infrastructure\RepositoriesElasticSearch\Constants\SortCriteria;
use Infrastructure\RepositoriesElasticSearch\Constants\SortDirection;
use Infrastructure\RepositoriesElasticSearch\ElasticSearchBaseRepository;

class ElasticSearchProductBaseRepository extends ElasticSearchBaseRepository
{

    const GENERIC_ID_SPAIN = '300';
    const CODE_STORE_TRAVEL_ES = "131";
    const CODE_STORE_RETAIL_ES = "155";
    const ATTRIBUTE_ID_TRAVEL = "339";
    const ATTRIBUTE_ID_RETAIL = "290";

    const FIELD_ATTRIBUTES_ID = "attributes.id";
    const INITIAL_DATE_MIN = "initialDateMin";
    const FINAL_DATE_MAX = "finalDateMax";
    const CAMPAIGN_START = "campaignStart";
    const CAMPAIGN_END = "campaignEnd";
    const LTE = "lte";
    const GT = "gt";
    const GTE = "gte";
    const PAUSED = "paused";
    const ACTIVE = "active";

    const VISIBILITIES_FIELD = 'visibilities';
    const SEPARATOR_CHARACTER = '.';
    const STORE_ID_FIELD = 'storeId';
    const INITIAL_DATE_FIELD = 'initialDate';
    const FINAL_DATE_FIELD = 'finalDate';
    const REVIEW_FIELD = 'review';
    const RANKING_FIELD = 'ranking';
    const CREATE_DATE_FIELD = 'createDate';
    const SORT_DATE_FIELD = 'sortDate';
    const RANK_FIELD = 'rank';
    const TOP_DEAL_FIELD = 'topDeal';
    const PERMANENT_DEAL_FIELD = 'permanentDeal';
    const SORT_PRICE_FIELD = 'sortPrice';
    const RANK_MOBILE_FIELD = 'rankMobile';
    const GEO_DISTANCE_FIELD = '_geo_distance';

    const QUERY_PARAM = 'query';
    const ORDER_PARAM = 'order';
    const LOCATIONS_GEO_POS_FIELD = 'locations.geoPos';
    const ASC_PARAM = 'asc';
    const KM_PARAM = 'km';

    protected $idStore = null;
    protected $attributes = null;
    protected $includeNationals = false;
    protected $start = null;
    protected $end = null;

    /**
     * @param  null              $type
     * @param  null              $params
     * @return array|null|string
     */
    protected function setSort($type=null, $params=null)
    {
        $orderFields = null;
        switch ($type) {
            case SortCriteria::SORT_NEWS;
                $orderFields = array(
                    self::SORT_DATE_FIELD => SortDirection::DESC
                );
                break;
            case SortCriteria::SORT_PRICE;
                $orderFields = array(
                    self::SORT_PRICE_FIELD => SortDirection::ASC
                );
                break;
            case SortCriteria::SORT_MOBILE;
                $orderFields = array(
                    self::RANK_MOBILE_FIELD => SortDirection::ASC
                );
                break;
            case SortCriteria::SORT_GPS;
                if (!is_null($params->lat) && !is_null($params->lon)) {
                    $sort = new \stdClass();
                    $sort->{self::GEO_DISTANCE_FIELD} = new \stdClass();
                    $sort->{self::GEO_DISTANCE_FIELD}->{self::LOCATIONS_GEO_POS_FIELD} = new \stdClass();
                    $sort->{self::GEO_DISTANCE_FIELD}->{self::LOCATIONS_GEO_POS_FIELD}->lat = $params->lat;
                    $sort->{self::GEO_DISTANCE_FIELD}->{self::LOCATIONS_GEO_POS_FIELD}->lon = $params->lon;
                    $sort->{self::GEO_DISTANCE_FIELD}->order = self::ASC_PARAM;
                    $sort->{self::GEO_DISTANCE_FIELD}->unit = self::KM_PARAM;
                    $orderFields = json_encode($sort);
                }
                break;
            case SortCriteria::RANKING_DESC;
                $orderFields = array(
                    self::REVIEW_FIELD .self::SEPARATOR_CHARACTER. self::RANKING_FIELD => SortDirection::DESC
                );
                break;
            case SortCriteria::RANKING_ASC;
                $orderFields = array(self::REVIEW_FIELD .self::SEPARATOR_CHARACTER. self::RANKING_FIELD => SortDirection::ASC);
                break;
            case SortCriteria::LEGACY_SORT;
            default:
                $orderFields[] = array(
                    self::PERMANENT_DEAL_FIELD => array(self::ORDER_PARAM => SortDirection::ASC)
                );
                $orderFields[] = array(
                    self::TOP_DEAL_FIELD => array(self::ORDER_PARAM => SortDirection::DESC)
                );
                $orderFields[] = array(
                    self::RANK_FIELD => array(self::ORDER_PARAM => SortDirection::DESC)
                );
                $orderFields[] = array(
                    self::REVIEW_FIELD.self::SEPARATOR_CHARACTER.self::RANKING_FIELD => array(self::ORDER_PARAM => SortDirection::ASC)
                );
                $orderFields[] = array(
                    self::SORT_DATE_FIELD => array(self::ORDER_PARAM => SortDirection::DESC)
                );
                $orderFields[] = array(
                    self::INITIAL_DATE_MIN => array(self::ORDER_PARAM => SortDirection::ASC)
                );
                $orderFields[] = array(
                    self::FINAL_DATE_MAX => array(self::ORDER_PARAM => SortDirection::DESC)
                );
                $orderFields[] = array(
                    self::CAMPAIGN_START => array(self::ORDER_PARAM => SortDirection::ASC)
                );
                $orderFields[] = array(
                    self::CAMPAIGN_END => array(self::ORDER_PARAM => SortDirection::DESC)
                );
                $orderFields[] = array(
                    self::CREATE_DATE_FIELD => array(self::ORDER_PARAM => SortDirection::DESC)
                );
        }

        return $orderFields;
    }

    private function _constructVisibilityCondition($storeId)
    {
        $fieldVisibilityStoreId = self::VISIBILITIES_FIELD . self::SEPARATOR_CHARACTER . self::STORE_ID_FIELD;
        $fieldVisibilityInitialDate = self::VISIBILITIES_FIELD . self::SEPARATOR_CHARACTER . self::INITIAL_DATE_FIELD;
        $fieldVisibilityFinalDate = self::VISIBILITIES_FIELD . self::SEPARATOR_CHARACTER . self::FINAL_DATE_FIELD;

        $storeFilter = $this->_createNestedCondition(self::VISIBILITIES_FIELD, self::QUERY_PARAM); //return nested
        $storeFilter->nested->query->bool->must = array();

        $visibilityStoreId = $this->_createMatchCondition($fieldVisibilityStoreId, $storeId); //return match

        $visibilityInitialDate = new \stdClass();
        $visibilityInitialDate->range = new \stdClass();
        $visibilityInitialDate->range->$fieldVisibilityInitialDate = new \stdClass();
        $visibilityInitialDate->range->$fieldVisibilityInitialDate->lte = $this->start;

        $visibilityFinalDate = new \stdClass();
        $visibilityFinalDate->range = new \stdClass();
        $visibilityFinalDate->range->$fieldVisibilityFinalDate = new \stdClass();
        $visibilityFinalDate->range->$fieldVisibilityFinalDate->gte = $this->end;

        $storeFilter->nested->query->bool->must[0] = $visibilityStoreId;
        $storeFilter->nested->query->bool->must[1] = $visibilityInitialDate;
        $storeFilter->nested->query->bool->must[2] = $visibilityFinalDate;

        return $storeFilter;
    }

    private function _createNestedCondition($path, $nestedType)
    {
        $conditionObject = new \stdClass();
        $conditionObject->nested = new \stdClass();
        $conditionObject->nested->path = $path;
        $conditionObject->nested->$nestedType = new \stdClass();
        $conditionObject->nested->$nestedType->bool = new \stdClass();

        return $conditionObject;
    }

    private function _createRangeCondition($field, $condition, $value)
    {
        $conditionObject = new \stdClass();
        $conditionObject->range = new \stdClass();
        $conditionObject->range->$field = new \stdClass();
        $conditionObject->range->$field->$condition = $value;

        return $conditionObject;
    }

    private function _createMatchCondition($field, $value)
    {
        $conditionObject = new \stdClass();
        $conditionObject->match = new \stdClass();
        $conditionObject->match->$field = $value;

        return $conditionObject;
    }

    protected function getStoresIds()
    {
        $storeIds = new \ArrayObject();
        if ($this->idStore) {
            $storeIds->append($this->idStore);
        }

        if ($this->includeNationals) {
            $storeIds->append(self::GENERIC_ID_SPAIN);
            $storeIds->append(self::CODE_STORE_RETAIL_ES);
            $storeIds->append(self::CODE_STORE_TRAVEL_ES);
        }

        return $storeIds;
    }

    /**
     * Return condition object for type of deals
     * @return \stdClass
     */
    private function _getTypeDealConditions()
    {
        $pausedCondition = $this->_createMatchCondition(self::PAUSED, 0);
        $activeCondition = $this->_createMatchCondition(self::ACTIVE, 1);

        $propertiesObject = array();
        $propertiesObject[] = $pausedCondition;
        $propertiesObject[] = $activeCondition;

        return $propertiesObject;
    }

    /**
     * Return object with campaignStart and Campaign end conditions for Permanent Deals.
     * @param  int       $initialDate
     * @param  int       $finalDate
     * @param  bool      $allFlashDeals Condition used to get all flash products with valid dates.
     * @return \stdClass
     */
    private function _getPermanentDatesObject($initialDate = 0, $finalDate = 0, $allFlashDeals = false)
    {
        $startField = $allFlashDeals ? self::INITIAL_DATE_MIN : self::CAMPAIGN_START;
        $finalField = $allFlashDeals ? self::FINAL_DATE_MAX : self::CAMPAIGN_END;
        $permDatesObject = array();

        //CampaignStart object
        $startObject = $this->_createRangeCondition($startField, self::LTE, $initialDate);

        //Validating campaignStart >0
        $validStartObject = $this->_createRangeCondition($startField, self::GT, 0);

        //CampaignEnd object
        $endObject = $this->_createRangeCondition($finalField, self::GTE, $finalDate);

        $permDatesObject[] = $startObject;
        $permDatesObject[] = $validStartObject;
        $permDatesObject[] = $endObject;

        return $permDatesObject;
    }

    /**
     * @return array
     */
    protected function visibilitiesFlash()
    {
        $flashObject = new \stdClass();
        $flashObject->must = array();

        $attributesObject = array();
        $attributesObject[0] = $this->_createRangeCondition(self::INITIAL_DATE_MIN, self::LTE, $this->start);
        $attributesObject[1] = $this->_createRangeCondition(self::FINAL_DATE_MAX, self::GTE, $this->end);

        //Create conditions for visibilities storeId
        if ($storeIds = $this->getStoresIds()) {
            foreach ($storeIds as $storeId) {
                $flashObject->must[] = $this->_constructVisibilityCondition($storeId);
            }
        }

        //attribute directory
        if (!empty($this->attributes)) {
            $flashObject->must[] = $this->_createAttributesCondition((array) $this->attributes);
        }

        //AND condition for attributes and store visibility
        if (!empty($attributesObject)) {
            $flashObject->must[] = $attributesObject;
        }

        //Create conditions for apply this filter to flash deals
        $flashObject->must[] = $this->_getTypeDealConditions();

        return (array) $flashObject;
    }

    /**
     * @return array
     */
    protected function visibilitiesPermanent()
    {
        //Object With all permanent Conditions
        $permObject = new \stdClass();
        $permObject->must = array();
        $attributesPermObject = null;
        $storesObject = null;
        //For permanents we need storeAttributeId instead storeId
        $attributes = (array) $this->attributes;
        $id = 0;

        if ($this->includeNationals) {
            $attributes[$id] = $this->idStore;
            //add generic
            $attributes[++$id] = self::ATTRIBUTE_ID_RETAIL;
            $attributes[++$id] = self::ATTRIBUTE_ID_TRAVEL;
        }

        //Create conditions for Attributes
        if (!empty($attributes)) {
            $attributesPermObject = $this->_createAttributesCondition($attributes);
        }
        if ($attributes) {
            foreach ((array) $this->attributes as $id) {
                $attributes[++$id] = $id;
            }
        }
        if ($this->includeNationals) {
            $storesObject = $this->_createAttributesCondition($attributes, 1);
            $permObject->must[] = $storesObject;
        }

        //Permanent Dates
        $permanentDates = $this->_getPermanentDatesObject($this->start, $this->end);
        //Create conditions for apply this filter to permanent deals
        $permPropertiesObject = $this->_getTypeDealConditions();

        if (!is_null($attributesPermObject)) {
            $permObject->must[] = $attributesPermObject;
        }
        $permObject->must[] = $permanentDates;
        $permObject->must[] = $permPropertiesObject;

        return (array) $permObject;
    }

    /**
     * Object with attributes to filter.
     * @param $attributes
     * @param  int       $isStore Only true in permanent Stores
     * @return \stdClass
     */
    protected function _createAttributesCondition($attributes, $isStore = 0)
    {
        $attributesFilter = array();

        if ($isStore) {
            $attributes[] = self::GENERIC_ID_SPAIN;
        }

        $attributes = array_unique($attributes);
        foreach ($attributes as $attr) {
            $attrFilter = new \stdClass();
            $attrFilter->match = new \stdClass();
            $attrFilter->match->{self::FIELD_ATTRIBUTES_ID} = $attr;

            $attributesFilter[] = $attrFilter;
        }
        $attributesObject = $this->_createNestedCondition("attributes", self::QUERY_PARAM);
        $attributesObject->nested->query->bool->must = $attributesFilter;

        return $attributesObject;
    }
}
