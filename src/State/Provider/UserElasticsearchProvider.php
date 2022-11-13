<?php

namespace App\State\Provider;

use ApiPlatform\Doctrine\Orm\Paginator;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\ArrayPaginator;
use ApiPlatform\State\Pagination\Pagination;
use ApiPlatform\State\Pagination\PaginatorInterface;
use ApiPlatform\State\ProviderInterface;
use App\Entity\User;
use App\Service\SearchService;
use App\State\PaginationMetaDataTrait;

class UserElasticsearchProvider implements ProviderInterface
{
    use PaginationMetaDataTrait;

    public const INDEX_NAME = 'user';

    public function __construct(readonly private SearchService $searchService,
                                readonly private Pagination    $pagination
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {

        if (!isset($context['filters']) || !isset($context['filters']['search'])) {
            throw new \Exception("Search param in query must be provided");
        }

        $pagination = $this->getPaginationMetaData($operation, $context);
        $search = $context['filters']['search'];


        $this->searchService->setIndex(self::INDEX_NAME);
        $this->searchService->setSourceFields(['id', 'name', 'address', 'activeJobPosition§']);
        $this->searchService->setOrder(['displayOrder' => 'desc']);

        return $this->searchService->search($search, $pagination);

    }

}
