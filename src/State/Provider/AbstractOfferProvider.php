<?php

namespace App\State\Provider;

use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\ArrayPaginator;
use ApiPlatform\State\Pagination\Pagination;
use ApiPlatform\State\Pagination\PaginatorInterface;
use ApiPlatform\State\ProviderInterface;
use App\Entity\User;
use App\Service\DisplayOrderService;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class AbstractOfferProvider implements ProviderInterface
{
    public function __construct(readonly protected CollectionProvider    $collectionProvider,
                                readonly protected TokenStorageInterface $tokenStorage,
                                readonly protected DisplayOrderService   $orderService,
                                readonly protected Pagination            $pagination
    )
    {
    }


    abstract public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null;

    protected function getRelatedUser(): UserInterface
    {
        return $this->tokenStorage->getToken()->getUser();
    }

    protected function makePagination(array $data, Operation $operation): PaginatorInterface
    {
        list($limit, $offset) = $this->getPaginationMetaData($operation);
        return new ArrayPaginator(
            $data,
            $offset,
            $limit
        );
    }

    private function getPaginationMetaData(Operation $operation): array
    {
        return [
            $this->pagination->getLimit($operation),
            $this->pagination->getOffset($operation)
        ];
    }

}