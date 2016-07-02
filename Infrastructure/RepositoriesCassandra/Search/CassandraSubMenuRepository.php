<?php

namespace Infrastructure\RepositoriesCassandra\Search;

use Domain\DomainMenus\Exceptions\DuplicateItemException;
use Domain\DomainMenus\Factories\MenuFactory;
use Domain\DomainMenus\Objects\MainMenuCollection;
use Domain\DomainMenus\Repositories\SubMenuRepository;
use Domain\DomainProduct\Objects\Search\ProductByStore\AttributeSearchCountCollection;
use Infrastructure\RepositoriesCassandra\CassandraBaseRepository;
use \Exception as Exception;

class CassandraSubMenuRepository extends CassandraBaseRepository implements SubMenuRepository
{
    const TYPE_PARTNERS_ROOT = 'pa-root';

    /**
     * @param string $idAttr
     * @param AttributeSearchCountCollection $listCount
     * @return MainMenuCollection
     */
    public function getSubMenuByAttrParent($idAttr,AttributeSearchCountCollection $listCount)
    {
        $attribute = $this->getAttributeById($idAttr);
        $parentAttr = $this->getParentAttribute($idAttr);
        if ($attribute['name'] == self::TYPE_PARTNERS_ROOT) {
            /** partners/barcelona/ **/
            $attributes = $this->getAttributeHierarchy($idAttr);
            $transvarsales = $this->getAttributesTranversalsByAttrId($idAttr);
            $attributes = array_replace_recursive($attributes, $transvarsales);
            $attributes = $this->sortByField($attributes, 'sort');
        } elseif ($parentAttr['name'] == self::TYPE_PARTNERS_ROOT) {
            /** partners/donde-viajar/barcelona/ */
            $attributes = $this->getAttributeHierarchy($idAttr, $parentAttr['id']);
            $transvarsales = $this->getAttributesTranversalsByAttrId($idAttr, true);
            $attributes = array_replace_recursive($attributes, $transvarsales);
            $attributes = $this->sortByField($attributes, 'esTag');
        } else {
            /** partners/donde-viajar/barcelona/.../ or partners/donde-viajar/barcelona/.../.../ **/
            $attributes = $this->getAttributeHierarchy($idAttr, $parentAttr['id'], true);
            $transvarsales = $this->getAttributesTranversalsByAttrId($idAttr, true);
            $attributes = array_replace_recursive($attributes, $transvarsales);
            $attributes = $this->sortByField($attributes, 'esTag');
        }

        uasort($attributes, (function ($a, $b) {
            if ($a['sort'] == self::TYPE_PARTNERS_ROOT) {
                return -1;
            } else {
                return +1;
            }
        }));

        $menuCurrent = MenuFactory::build($attribute['esTag'],$attribute['esUrl'],$attribute['esUrl']);
        $menuParent = MenuFactory::build($parentAttr['esTag'],$parentAttr['esUrl'],$attribute['esUrl']);


        return $this->buildMenuCollection($attributes,$listCount,$menuCurrent,$menuParent);
    }

    private function sortByField($arrayDate, $field)
    {
        uasort($arrayDate, (function ($a, $b) use ($field) {
            if ($a[$field] < $b[$field]) {
                return -1;
            } else {
                return +1;
            }
        }));
        return $arrayDate;
    }

    /**
     * @param $idAttribute
     * @return array|null
     */
    private function getAttributeById($idAttribute)
    {
        return $this->getOneItemsByRowKey(
            'AttributesDirectory',
            $idAttribute
        );
    }

    /**
     * @param $id
     * @return array|null
     */
    public function getParentAttribute($id)
    {
        try {
            $parentId = $this->getRelAttributeDirectory($id);
            $attribute = $this->getAttributeById($parentId['parentId']);
            $attribute['id'] = $parentId['parentId'];
            return $attribute;
        } catch (Exception $e) {
            return null;
        }

    }

    /**
     * @param $id
     * @return array|null
     */
    public function getRelAttributeDirectory($id)
    {
        try {
            return $this->getOneItemsByRowKey(
                'RelAttributeDirectory',
                $id
            );
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @param $id
     * @return array|null
     */
    public function getClass($id)
    {
        try {
            return $this->getOneItemsByRowKey(
                'ClassDirectory',
                $id
            );
        } catch (Exception $e) {
            return null;
        }
    }


    /**
     * @param $attrId
     * @param null $excludeParentId
     * @param bool|false $allExpanded
     * @return array
     */
    private function getAttributeHierarchy($attrId, $excludeParentId = null, $allExpanded = false)
    {
        $attributes = $this->getAttributesByParentId($attrId);
        $serviceSort = $this->getSortedServicesAttributeIds('Partner');
        $transvarsales = $this->getAttributesTranversalsByAttrId($attrId);
        $attributes = array_replace_recursive($attributes, $transvarsales);
        $items = array();
        foreach ($attributes as $familyId => $family) {
            if ($family['isTag'] == "1") {
                $family['sort'] = (empty($serviceSort[$familyId])) ? 999 : $serviceSort[$familyId];
                $family['child'] = array();
                if (($familyId != $excludeParentId) && $allExpanded) {
                    $childrenObjs = $this->getAttributeHierarchy($familyId, null, $allExpanded);
                    $transvarsales = $this->getAttributesTranversalsByAttrId($familyId);
                    $family['child'] = array_replace_recursive($childrenObjs, $transvarsales);
                }
                $items[$familyId] = $family;
            }
        }

        return $items;
    }

    /**
     * Return ArrayObject with ArrayObject by ClassesAttributes, not city transversal
     * @param $attributeId
     * @param $excludeAttr
     * @return array
     */
    public function getAttributesTranversalsByAttrId($attributeId, $excludeAttr = false)
    {
        $relAttributeDirectory = $this->getRelAttributeDirectory($attributeId);
        $arrayAttributes = array();
        if ($relAttributeDirectory && !empty($relAttributeDirectory['transversalClassId'])) {
            $arrayClassesIds = (array)json_decode($relAttributeDirectory['transversalClassId']);
            foreach ($arrayClassesIds as $classesId) {
                $classArray = $this->getClass($classesId);
                if ($classArray) {
                    $class = array();
                    $class['isClassTranversal'] = true;
                    $class['id'] = $classesId;
                    $class['url'] = '#'; //legacy
                    $class['isTag'] = 1;
                    $class['esTag'] = $classArray['esTag']; //legacy
                    $class['name'] = 'trans-' . $classArray['esTag']; //legacy
                    $class['tag'] = $classArray['esTag'];
                    $class['classId'] = $classArray['classId'];
                    $class['sort'] = 0;
                    $rawAttributes = $this->getAttributesByClassId($class->classId);
                    if ($excludeAttr && !empty($rawAttributes[$attributeId])) {
                        unset($rawAttributes[$attributeId]);
                    }
                    $class['child'] = $rawAttributes;
                    $arrayAttributes[$classesId] = $class;
                }

            }
        }
        return $arrayAttributes;
    }

    /**
     * Returns all the attributes with the given parentId
     * @param int $parentId
     * @return array
     */
    public function getAttributesByParentId($parentId)
    {
        $children = $this->getByRowIndexed('RelAttributeDirectory', 'parentId', $parentId);
        return $this->getMultipleItemsByRowKey('AttributesDirectory', array_keys(iterator_to_array($children)));
    }

    /**
     * Return an array of sorted services attributes ids
     * @param string $classeName
     * @return array sorted attributes ids
     */
    public function getSortedServicesAttributeIds($classeName = 'Category')
    {
        $category = $this->getClass($classeName);
        $attributeIds = $this->getOneItemsByRowKey('RelClassAttributeDirectory', $category['classId']);

        uasort($attributeIds, function ($a, $b) {
            if ($a == $b) {
                return 0;
            }
            if ($a == 0 && $b != 0) {
                return 1;
            }
            if ($b == 0 && $a != 0) {
                return -1;
            }

            return ($a < $b) ? -1 : 1;
        });
        return $attributeIds;
    }

    /**
     * Returns all the attributes owned by the given classId
     * @param int $classId
     * @return array
     */
    public function getAttributesByClassId($classId)
    {
        $attributes = $this->getOneItemsByRowKey('RelClassAttributeDirectory', $classId);
        $attributesArray = $this->getMultipleItemsByRowKey('AttributesDirectory', array_keys($attributes));
        foreach ($attributes as $id => $sort) {
            $attributesArray[$id]['sort'] = $sort;
        }
        return $attributesArray;
    }

    /**
     * @param $rawMenuList
     * @param $listCount
     * @param $currentAttribute
     * @param $currentParentAttribute
     * @return MainMenuCollection
     * @throws Exception
     */
    private function buildMenuCollection($rawMenuList, $listCount = null, $currentAttribute=null,$currentParentAttribute=null)
    {
        $listMenu = new MainMenuCollection($currentAttribute,$currentParentAttribute);
        foreach ($rawMenuList as $id => $item) {
            $subMenu = null;
            $count = 0;
            if (!empty($listCount) && $listCount->has($id) ) {
                $count = $listCount->get($id);
            }
            if (!empty($item['child'])){
                $subMenu = $this->buildMenuCollection($item['child'],$listCount);
            }
            $urlMenu = $item['esUrl']; //TODO absolete Url
            $menuItem = MenuFactory::build(
                $item['esTag'],
                $item['esUrl'],
                $urlMenu,
                (!empty($item['order']))?$item['order']:0,
                $id,
                $count,
                $subMenu
            );
            try {
                $listMenu->add($menuItem);
            } catch (DuplicateItemException $e) {
                //Is not necessary add the item twice.
            }
        }
        return $listMenu;
    }

}
