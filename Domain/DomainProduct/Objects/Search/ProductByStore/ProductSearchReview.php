<?php


namespace Domain\DomainProduct\Objects\Search\ProductByStore;

class ProductSearchReview
{

    public function __construct($id, $optionsCount, $samples, $ranking)
    {
        $this->id = $id;
        $this->optionsCount = $optionsCount;
        $this->samples = $samples;
        $this->ranking = $ranking;
    }

    private $id;

    /** @var int */
    private $optionsCount;

    /** @var  int
     base 100 */
    private $samples;

    /** @var  int */
    private $ranking;
}
