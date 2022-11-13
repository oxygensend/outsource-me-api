<?php

namespace App\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\Pagination;
use ApiPlatform\State\ProviderInterface;
use App\Service\SearchService;
use App\State\PaginationMetaDataTrait;

class JobOfferElasticsearchProvider implements ProviderInterface
{
    use PaginationMetaDataTrait;

    public const INDEX_NAME = 'job_offer';

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
        $this->searchService->setSourceFields(['user', 'name', 'shortDescription', 'slug']);
        $this->searchService->setOrder(['displayOrder' => 'desc']);

        return $this->searchService->search($search, $pagination);

    }
}