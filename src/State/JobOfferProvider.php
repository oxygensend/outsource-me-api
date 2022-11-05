<?php

namespace App\State;

use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\ArrayPaginator;
use ApiPlatform\State\Pagination\Pagination;
use ApiPlatform\State\ProviderInterface;
use App\Service\JobOfferDisplayOrderService;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class JobOfferProvider implements ProviderInterface
{
    public function __construct(readonly private CollectionProvider          $collectionProvider,
                                readonly private TokenStorageInterface       $tokenStorage,
                                readonly private JobOfferDisplayOrderService $orderService,
                                readonly private Pagination                  $pagination
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {

        $user = $this->tokenStorage->getToken()->getUser();

        $limit = $this->pagination->getLimit($operation);
        $offset = $this->pagination->getOffset($operation);

        $jobOffers = $this->collectionProvider->provide($operation, $uriVariables, $context);

        if (isset($context['filters']) && isset($context['filters']['order']) && $context['filters']['order'] === 'for-you') {
            $jobOffers = $this->orderService->calculateForYouDisplayOrder($jobOffers, $user);
        }

        return new ArrayPaginator(
            $jobOffers,
            $offset,
            $limit
        );
    }
}
