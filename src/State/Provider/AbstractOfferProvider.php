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
use App\State\PaginationMetaDataTrait;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class AbstractOfferProvider implements ProviderInterface
{
    use PaginationMetaDataTrait;

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

    protected function makePagination(array $data, Operation $operation, array $context): PaginatorInterface
    {
        $pagination = $this->getPaginationMetaData($operation, $context);

        return new ArrayPaginator(
            $data,
            $pagination['offset'],
            $pagination['limit']
        );
    }


}