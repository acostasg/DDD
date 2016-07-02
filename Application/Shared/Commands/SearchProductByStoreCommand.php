<?php

namespace Application\Shared\Commands;

use Application\Shared\Commands\InternalCommands\GetSubMenuInternalCommand;
use Application\Shared\Requests\SearchProductByStoreRequest;
use Application\Shared\Responses\SearchProductByStoreResponse;
use Domain\DomainProduct\Objects\Search\ProductByStore\ProductSearchCollection;
use Domain\DomainProduct\Repositories\ProductSearchRepository;
use Infrastructure\RepositoriesCassandra\CassandraBaseRepository;
use \Exception as Exception;

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
    }

    /**
     * @param  SearchProductByStoreRequest  $request
     * @return SearchProductByStoreResponse
     * @throws \Exception
     */
    public function executxe(SearchProductByStoreRequest $request)
    {
        /** @var ProductSearchCollection $productSearchCollection */
        $productSearchCollection = $this->productRepository->SearchProductByStore($request->codeWebSite(), $request->idStore(),
            $request->attributes(), $request->currentPage(), $request->limitPage());

        if (!$productSearchCollection) {
            throw new Exception('Not product/s found');
        }

        //command to get menu @TODO

        //command to get submenu
        $cmd = new GetSubMenuInternalCommand($this->subMenuRepository);
        $subMenuCollection = $cmd->execute($request->parentAttribute(), $productSearchCollection->attributeCountCollection());

        $totalPage = ceil($productSearchCollection->totalSearchProducts()/$request->limitPage());

        return new SearchProductByStoreResponse($productSearchCollection, $request->currentPage(), $totalPage,
            $subMenuCollection, $request->idStore(), $request->attributes());
    }
}
