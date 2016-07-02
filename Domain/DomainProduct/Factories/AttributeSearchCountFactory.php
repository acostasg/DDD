<?php

namespace Domain\DomainProduct\Factories;

use Domain\DomainProduct\Objects\Search\ProductByStore\AttributeSearchCount;
use Domain\DomainShared\Objects\Id;

class AttributeSearchCountFactory
{
    public static function build(
       $count,
       $id = null
    ) {
        if (is_null($count)) {
            throw new \Exception('The count is required in AttributeSearchCount object');
        }

        $id = new Id($id);

        return new AttributeSearchCount(
            $id,
            $count
        );
    }
}
