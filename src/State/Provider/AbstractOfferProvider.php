<?php

namespace App\State\Provider;

use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\ArrayPaginator;
use ApiPlatform\State\Pagination\Pagination;
use ApiPlatform\State\Pagination\PaginatorInterface;
use ApiPlatform\State\ProviderInterface;
use App\Cache\RedisCacheMaker;
use App\Entity\User;
use App\Service\DisplayOrderService;
use App\State\PaginationMetaDataTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\SerializerInterface;

abstract class AbstractOfferProvider implements ProviderInterface
{
    use PaginationMetaDataTrait;

    public const CACHE_LIMIT = 10800;

    public function __construct(readonly protected CollectionProvider    $collectionProvider,
                                readonly protected TokenStorageInterface $tokenStorage,
                                readonly protected DisplayOrderService   $orderService,
                                readonly protected Pagination            $pagination,
                                readonly protected RedisCacheMaker       $cacheMaker,
                                readonly protected SerializerInterface   $serializer,
                                readonly protected RequestStack $requestStack,
    )
    {
    }


    abstract public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null;

    protected function getRelatedUser(): UserInterface|User
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

    protected function deserialize(string $data): array
    {
        return json_decode($data, true);
    }

    protected function serialize(array $data, array $context): string
    {
        return $this->serializer->serialize($data, 'json', $context);
    }


}