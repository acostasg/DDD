<?php

namespace Application\ApplicationShared\Commands;

use Application\ApplicationShared\Commands\InternalCommands\GetSubMenuInternalCommand;
use Application\ApplicationShared\Requests\SearchProductByStoreRequest;
use Application\ApplicationShared\Responses\SearchProductByStoreResponse;
use Domain\DomainProduct\Objects\Search\ProductByStore\ProductSearchCollection;
use Domain\DomainProduct\Repositories\ProductSearchRepository;
use Infrastructure\RepositoriesCassandra\CassandraBaseRepository;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * Created by PhpStorm.
 * User: albert
 * Date: 26/01/16
 * Time: 15:51
 */
class SearchProductByStoreCommand
{
    /**
     * @var ProductSearchRepository
     */
    private $productRepository;
    /**
     * @var CassandraBaseRepository
     */
    private $subMenuRepository;

    /**
     * SearchProductByStoreCommand constructor.
     * @param ProductSearchRepository $productRepository
     * @param CassandraBaseRepository $subMenuRepository
     */
    public function __construct(
        ProductSearchRepository $productRepository,
        CassandraBaseRepository $subMenuRepository
    ) {
        $this->productRepository = $productRepository;
        $this->subMenuRepository = $subMenuRepository;
x    }

    /**
     * @param  SearchProductByStoreRequest  $request
     * @return SearchProductByStoreResponse
     */
    public function executxe(SearchProductByStoreRequest $request)
    {
        /** @var ProductSearchCollection $productSearchCollection */
        $productSearchCollection = $this->productRepository->SearchProductByStore($request->codeWebSite(), $request->idStore(),
            $request->attributes(), $request->currentPage(), $request->limitPage());

        if (!$productSearchCollection) {
            throw new \Exception('Not product/s found');
        }

        //command to get menu

        //command to get submenu
        $cmd = new GetSubMenuInternalCommand($this->subMenuRepository);
        $subMenuCollection = $cmd->execute($request->parentAttribute(), $productSearchCollection->attributeCountCollection());

        $totalPage = ceil($productSearchCollection->totalSearchProducts()/$request->limitPage());

        return new SearchProductByStoreResponse($productSearchCollection, $request->currentPage(), $totalPage,
            $subMenuCollection, $request->idStore(), $request->attributes());
    }
}
