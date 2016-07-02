<?php

namespace Infrastructure\Product\Search\Utils;

class ProductIndexerMapping
{


    private static $_api = array();

    /**
     * @param $product
     * @param $visibilities
     * @param array $children
     * @return array
     */
    public static function modelToDocument($product, $visibilities, $website, $children = array())
    {
        $document = array();
        $review = (empty($product->review) || (isset($product->review['samples']) && $product->review['samples'] == 0)) ? null : $product->review;

        //basic information
        $document['id'] = $product->id;
        $document["sku"] = $product->sku;
        $document['title'] = (isset($product->multiTitle) && array_key_exists($website->id, $product->multiTitle)) ? $product->multiTitle[$website->id] : $product->title;
        $document['shortTitle'] = (htmlspecialchars_decode($product->shortTitle));
        $document['description'] = (htmlspecialchars_decode($product->description));
        $document['featured'] = (htmlspecialchars_decode($product->featured));
        $document['review'] = $review;
        $document['urlKey'] = $product->urlKey;

        $document['masterCategory'] = $product->masterCategory;

        $hasNationalAttr = false;
        //Attributes
        $attributes = $product->getAttributes(true);
        foreach ($attributes as $key => $attr) {
            $document['attributesIds'][] = array('id' => $key);
            $document['attributes'][] = array(
                'id' => $key,
                'url' => isset($attr["{$website->locale}Url"]) ? $attr["{$website->locale}Url"] : "",
                'name' => isset($attr["{$website->locale}Tag"]) ? $attr["{$website->locale}Tag"] : ""
            );
            //Used to generate Url
            if ($key == $website->attributeId) {
                $hasNationalAttr = true;
            }
        }

        //Categories
        $categories = $product->categories;
        if (!empty($categories)) {
            foreach ($categories as $cat) {
                $document['categories'][] = array('name' => $cat['name']);
            }
        }

        //campaign information
        $document['accountName'] = $product->tradeName;
        $document['highLight'] = (htmlspecialchars_decode($product->highLight));
        $document['ownerCampaign'] = $product->title;
        $document['nameCampaign'] = $product->campaignName;
        $document['terms'] = (htmlspecialchars_decode($product->terms));
        $document['shortDescription'] = (htmlspecialchars_decode($product->shortDescription));
        $document['otherTerms'] = (htmlspecialchars_decode($product->otherTerms));
        $document['productType'] = $product->type;
        $document['providerId'] = $product->providerId;

        //metas
        $document['metaKeywords'] = $product->metaKeywords;
        $document['metaDescription'] = $product->metaDescription;
        $document['metaTitle'] = $product->metaTitle;

        //map
        $document["href"] = $product->urlKey;
        $document["img"] = $product->image;
        $document["imgUrlSmall"] = $product->imgUrlSmall;

        $document["dealFamily"] = $product->dealFamily;
        $document["tradeName"] = $product->tradeName;
        $document["address"] = (isset($product->address)) ? $product->address : '';

        //filters
        $document["paused"] = (boolean)($product->paused) ? true : false;
        $document["permanentDeal"] = (boolean)($product->permanentDeal) ? true : false;
        $document["active"] = (boolean)($product->active) ? true : false;

        $document['prices'] = array(
            'specialPrice' => (float)$product->specialPrice,
            'price' => (float)$product->price,
            'discount' => (float)$product->discount,
            'hidePrice' => $product->hidePrice
        );

        //directions, it's array includer latitude and longitude
        $locations = \Zend_Json::decode($product->geoLocations);
        if (is_array($locations)) {
            foreach ($locations as $id => $location) {
                $country = '';
                $district_name = '';
                if (array_key_exists('country_name', $location)) {
                    $country = $location['country_name'];
                }
                if (array_key_exists('district_name', $location)) {
                    $district_name = $location['district_name'];
                }

                $document['locations'][] = array(
                    'id' => $id,
                    'address' => isset($location['address']) ? $location['address'] : '',
                    'countryName' => $country,
                    'district_name' => $district_name,
                    'geoPos' => array('lat' => $location['latitude'], 'lon' => $location['longitude'])
                );
            }
        }

        $document['locationSummary'] = $product->getLocationSummaryProduct();

        foreach ($children as $key => $child) {
            $document['products'][] = array(
                'id' => $child->id,
                'shortTitle' => $child->shortTitle,
                'title' => $child->title,
                'image' => isset($child->mediaGallery[0]) ? $child->mediaGallery[0]['image'] : $product->image,
                'multiOptions' => (empty($child->multiOptions)) ? null : json_encode($child->multiOptions),
                'shoppingMultipleOf' => $child->shoppingMultipleOf,
                'stock' => $child->stock,
                'isInStock' => $child->isInStock,
                'prices' => array(
                    'specialPrice' => $child->specialPrice,
                    'price' => (float)$child->price,
                    'discount' => (float)$child->discount,
                    'hidePrice' => $child->hidePrice
                ),
                'shippingInfo' => $child->shippingInfo
            );
        }

        //Visibilities
        $sortDate = 0;
        $urlNational = null;
        $document['visibilities'] = array();

        foreach ($visibilities as $visibility) {

            $store = self::getApi('config')->cache(\Application_Cache_TTL::HIGH)->getStoreById($visibility->storeId);

            if ($visibility->initialDate > 0 && ($visibility->initialDate < $sortDate || $sortDate == 0)) {
                $sortDate = (int)$visibility->initialDate;
            }

            $isGenericStore = ($store->id == $website->defaultStoreId);
            $isdateNull = ($visibility->initialDate == 0 && $visibility->finalDate == 0);

            if ($product->permanentDeal && $isGenericStore && $hasNationalAttr) {
                $urlNational = self::getApi('product')->cache(\Appliction_Cache_TTL::MEDIUM)->getProductUrl($product->urlKey,
                    $product->getAttributes(), $website->id,$website->defaultStoreId);
            } //Visibility without dates its INVALID
            elseif (!$isdateNull) {
                $document['visibilities'][] = array(
                    'storeId' => $visibility->storeId,
                    'finalDate' => (int)$visibility->finalDate,
                    'initialDate' => (int)$visibility->initialDate,
                    'url' => self::getApi('product')->cache(\Application_Cache_TTL::MEDIUM)->getProductStoreUrl($product->urlKey,
                        $store->id)
                );
                if ($isGenericStore && empty($urlNational) && $hasNationalAttr) {
                    $urlNational = self::getApi('product')->cache(\Application_Cache_TTL::MEDIUM)->getProductUrl($product->urlKey,
                        $product->getAttributes(), $website->id,$website->defaultStoreId);
                } elseif ($store->isNational) {
                    //If is national for the same website
                    $storeWebsite = self::getApi('config')->cache(\Application_Cache_TTL::HIGH)->getWebsiteByStoreId($store->id);
                    if ($storeWebsite->id == $website->id) {
                        //Retail & Travel
                        $urlNational = self::getApi('product')->cache(\Application_Cache_TTL::MEDIUM)->getProductStoreUrl($product->urlKey,
                            $store->id);
                    }
                }
            }
        }

        $document['urlNational'] = $urlNational;

        //If product is permanent and doesn't has flash visibilities it's necessary check product->attributes
        //to find store attributes and generate appropriate Url
        if ($product->permanentDeal && empty($document['visibilities'])) {
            $attributeValues = array_keys($attributes);
            sort($attributeValues);
            $stores = self::getApi('config')->cache(\Application_Cache_TTL::HIGH)->getStoresByAttributes($attributeValues,
                $website->id);
            foreach ($stores as $store) {
                $document['visibilities'][] = array(
                    'storeId' => $store->id,
                    'finalDate' => null,
                    'initialDate' => null,
                    'url' => self::getApi('product')->cache(\Application_Cache_TTL::MEDIUM)->getProductStoreUrl($product->urlKey,
                        $store->id)
                );
            }
        }

        if ($product->permanentDeal) {
            $sortDate = (int)$product->campaignStart;
            $document['campaignStart'] = (int)$product->campaignStart;
            $document['campaignEnd'] = (!empty($product->campaignEnd)) ? (int)$product->campaignEnd : 9999999999;
        }

        /*Date for sort in api mobile:
            - Permanents: CampaignStart
            - Flash: Min. visibilities dates */
        $document['sortDate'] = $sortDate;

        //Price for sort in api mobile, price 0 has to appear at the end.
        $document['sortPrice'] = (float)$product->specialPrice > 0 ? (float)$product->specialPrice : 99999;
        //topDeal and Rank is necessary to order by sel(grp_selection) in msite
        $document['rank'] = ($product->rank && $product->rank > 0) ? $product->rank : 99999;
        $document['rankMobile'] = ($product->rankMobile && $product->rankMobile > 0) ? $product->rankMobile : 99999;
        $document['topDeal'] = $product->topDeal;
        $document['createDate'] = (int)$product->createDate;

        $document['mediaGallery'] = json_encode($product->mediaGallery);

        $document['showExtraForm'] = ($product->showExtraForm) ? 1 : 0;
        $document['showBillingForm'] = ($product->showBillingForm) ? 1 : 0;
        $document['serviticket'] = ($product->serviticket) ? 1 : 0;
        $document['couponStartMode'] = $product->couponStartMode;
        $document['couponStartDate'] = $product->couponStartDate;
        $document['couponEndDate'] = $product->couponEndDate;
        $document['couponDuration'] = $product->couponDuration;
        $document['shoppingcartMax'] = (int)$product->shoppingcartMax;
        $document['shoppingcartMin'] = (int)$product->shoppingcartMin;
        $document['shoppingMultipleOf'] = (int)$product->shoppingMultipleOf;
        $document['instructionsIdPool'] = $product->instructionsIdPool;
        $document['usesIdpool'] = $product->usesIdpool;
        $document['successDetails'] = $product->successDetails;
        $document['layout'] = $product->layout;
        $document['insuranceProductId'] = $product->insuranceProductId;
        $document['campaignProdNum'] = $product->campaignProdNum;
        $document['initialDateMin'] = $product->initialDateMin;
        $document['finalDateMax'] = $product->finalDateMax;
        //WEB-11574 Se usa en el resultset en el feed_affiliates, no hace falta ponerlo en el modelo product
        $document['canonicalUrl'] = self::getApi('product')->cache(\Application_Cache_TTL::MEDIUM)->getProductPageCanonicalUrl($product->id, $website->id);

        return $document;
    }

    /**
     * @param array $document
     * @return \Application_Model_Product
     */
    public static function documentToModel($document)
    {
        $product = new \Application_Model_Product();

        //basic information
        $product->id = static::_checkKey($document, 'id');
        $product->sku = static::_checkKey($document, 'sku');
        $product->title = static::_checkKey($document, 'title');
        $product->shortTitle = static::_checkKey($document, 'shortTitle');
        $product->description = static::_checkKey($document, 'description');
        $product->featured = static::_checkKey($document, 'featured');
        $product->urlKey = static::_checkKey($document, 'urlKey');

        $product->review  = static::_checkKey($document, 'review');
        $product->masterCategory = static::_checkKey($document, 'masterCategory');

        //campaign information
        $product->tradeName = static::_checkKey($document, 'accountName');
        $product->highLight = static::_checkKey($document, 'highLight');
        $product->title = static::_checkKey($document, 'ownerCampaign');
        $product->campaignName = static::_checkKey($document, 'nameCampaign');
        $product->terms = static::_checkKey($document, 'terms');
        $product->shortDescription = static::_checkKey($document, 'shortDescription');
        $product->otherTerms = static::_checkKey($document, 'otherTerms');
        $product->type = static::_checkKey($document, 'productType');

        //Needed for msite to order by sel (grp_selection)
        $product->rank = static::_checkKey($document, 'rank');
        $product->rankMobile = static::_checkKey($document, 'rankMobile');
        $product->topDeal = static::_checkKey($document, 'topDeal');
        $product->createDate = static::_checkKey($document, 'createDate');

        //metas
        $product->metaKeywords = static::_checkKey($document, 'metaKeywords');
        $product->metaDescription = static::_checkKey($document, 'metaDescription');
        $product->metaTitle = static::_checkKey($document, 'metaTitle');

        //map
        $product->urlKey = static::_checkKey($document, "href");
        $product->image = static::_checkKey($document, "img");
        $product->imgUrlSmall = static::_checkKey($document, "imgUrlSmall");
        $product->dealFamily = static::_checkKey($document, "dealFamily");
        $product->tradeName = static::_checkKey($document, "tradeName");


        $product->price = (!$document['prices']['hidePrice']) ? $document['prices']['price'] : null;
        $product->specialPrice = $document['prices']['specialPrice'];
        $product->hidePrice = $document['prices']['hidePrice'];
        $product->discount = (!$document['prices']['hidePrice']) ? $document['prices']['discount'] : null;
        //filters
        $product->paused = $document["paused"];
        $product->permanentDeal = $document["permanentDeal"];
        $product->active = $document["active"];

        $product->address = static::_checkKey($document, "address");
        //(41.3903857|2.1741714|Carrer AusiÃ s Marc, 13, 08010, Barcelona)

        $locations = array();
        //{"ES-017729":{"latitude":40.424352,"longitude":-3.669522,"address":"C\/Goya, 116, 28009, madrid","country_name":""}}

        //geolocations
        if (static::_checkKey($document, 'locations')) {
            for ($i = 0; $i < count($document['locations']); $i++) {
                try {
                    $locations[$i]['latitude'] = $document['locations'][$i]['geoPos']['lat'];
                    $locations[$i]['longitude'] = $document['locations'][$i]['geoPos']['lon'];
                    $locations[$i]['address'] = $document['locations'][$i]['address'];

                } catch (Exception $e) {
                    continue;
                }
            }
            //directions, it's array includer latitude and longitude
            $product->geoLocations = json_encode($locations);
        }
        $attributes = array();
        if (isset($document['attributes'])) {
            for ($i = 0; $i < count($document['attributes']); $i++) {
                try {

                    $attributes[$i]['id'] = $document['attributes'][$i]['id'];
                    $attributes[$i]['url'] = $document['attributes'][$i]['url'];
                    $attributes[$i]['name'] = $document['attributes'][$i]['name'];

                } catch (Exception $e) {
                    continue;
                }
            }
            $product->attributes = $attributes;
        }

        $product->campaignStart = static::_checkKey($document, 'campaignStart');
        $product->campaignEnd = static::_checkKey($document, 'campaignEnd');

        $product->showExtraForm = static::_checkKey($document, 'showExtraForm');
        $product->showBillingForm = static::_checkKey($document, 'showBillingForm');
        $product->serviticket = static::_checkKey($document, 'serviticket');
        $product->couponStartMode = static::_checkKey($document, 'couponStartMode');
        $product->couponStartDate = static::_checkKey($document, 'couponStartDate');
        $product->couponEndDate = static::_checkKey($document, 'couponEndDate');
        $product->couponDuration = static::_checkKey($document, 'couponDuration');
        $product->shoppingcartMax = static::_checkKey($document, 'shoppingcartMax');
        $product->shoppingcartMin = static::_checkKey($document, 'shoppingcartMin');
        $product->shoppingMultipleOf = static::_checkKey($document, 'shoppingMultipleOf');
        $product->instructionsIdPool = static::_checkKey($document, 'instructionsIdPool');
        $product->usesIdpool = static::_checkKey($document, 'usesIdpool');
        $product->successDetails = static::_checkKey($document, 'successDetails');
        $product->layout = static::_checkKey($document, 'layout');
        $product->insuranceProductId = static::_checkKey($document, 'insuranceProductId');
        $product->campaignProdNum = static::_checkKey($document, 'campaignProdNum');
        $product->initialDateMin = static::_checkKey($document, 'initialDateMin');
        $product->finalDateMax = static::_checkKey($document, 'finalDateMax');

        $product->locationSummary = static::_checkKey($document, 'locationSummary');

        $product->children = isset($document['products']) ? json_encode($document['products']) : null;

        $product->mediaGallery = static::_checkKey($document, 'mediaGallery');

        return $product;

    }

    private static function _checkKey($array, $key)
    {
        if (array_key_exists($key, $array)) {
            return $array[$key];
        }
        return null;
    }

    /**
     * Returns RPC client object
     *
     * @param string $type API name
     *
     * @return \Application_Rpc_Client_Fa
     */
    public static function getApi($type)
    {
        return \Application_Helper_ExternalApi::factory()->getApi($type);
    }

    /**
     * Set RPC client object, injected mock for PHPUnit
     *
     * @param array $objectArray API name and equal mocks
     * ['config'=>ObjectMock,'order'=>ObjectMock]
     *
     * @return \Application_Rpc_Client_Fa
     */
    public static function setApi($objectArray)
    {
        foreach ($objectArray as $type => $mock) {
            return \Application_Helper_ExternalApi::factory()->setApi($type,$mock);
        }
    }


}