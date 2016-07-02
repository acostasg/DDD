<?php

namespace Domain\DomainMenus\Objects;

use Domain\DomainMenus\Exceptions\DuplicateItemException;

class MainMenuCollection extends MenuCollection
{

    /** @var Menu */
    private $currentMenu = null;

    /** @var Menu */
    private $currentParentMenu = null;

    /**
     * MainMenuCollection constructor.
     * @param string|null $currentMenu
     * @param string|null $currentParentMenu
     */
    public function __construct($currentMenu=null,$currentParentMenu=null)
    {
        parent::__construct();
        $this->currentMenu = $currentMenu;
        $this->currentParentMenu = $currentParentMenu;
    }


    /**
     * Add Menu item to the collection in correct order
     * using Menu->order() attribute to sort
     *
     * @param Menu $object
     * @throws DuplicateItemException
     */
    public function add(Menu $object)
    {
        if ($this->inArray($object)) {
            throw new DuplicateItemException('The item is already in the Menu');
        }
        $tmpCollectionBefore = $this->collection;
        $tmpCollectionAfter = [];
        foreach ($this->collection as $key => $item) {
            if ($item->order() > $object->order()) {
                if (0 < $key) {
                    $tmpCollectionBefore = array_slice(
                        $this->collection,
                        0,
                        $key
                    );
                } else {
                    $tmpCollectionBefore = [];
                }
                $tmpCollectionAfter = array_slice(
                    $this->collection,
                    $key
                );
                break;
            }
        }
        $tmpCollectionBefore[] = $object;
        $this->collection = array_merge($tmpCollectionBefore, $tmpCollectionAfter);
    }

    /**
     * Search if the object is in the collection
     *
     * @param Menu $object
     * @return bool
     */
    private function inArray(Menu $object)
    {
        $inArray = false;
        foreach ($this->collection as $item) {
            if ($object->id() == $item->id()) {
                $inArray = true;
                break;
            }
        }

        return $inArray;
    }

    /**
     * @return Menu
     */
    public function currentMenu()
    {
        return $this->currentMenu;
    }

    /**
     * @return Menu
     */
    public function currentParentMenu()
    {
        return $this->currentParentMenu;
    }

}
