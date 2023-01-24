<?php

namespace App\Extensions\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\AboutUs;
use App\Entity\Application;
use App\Entity\Book;
use App\Entity\Category;
use App\Entity\Education;
use App\Entity\JobOffer;
use App\Entity\JobPosition;
use App\Entity\Notification;
use App\Entity\Technology;
use Doctrine\ORM\QueryBuilder;

class EnabledExtension implements QueryCollectionExtensionInterface
{
    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = []): void
    {
        $this->andWhere($queryBuilder, $resourceClass);
        $this->orderBy($queryBuilder, $resourceClass);
    }

    private function andWhere(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        if (AboutUs::class === $resourceClass) {

            $rootAlias = $queryBuilder->getRootAliases()[0];
            $queryBuilder->andWhere(sprintf('%s.enabled=true', $rootAlias));
        } else if (JobOffer::class === $resourceClass) {

//            $rootAlias = $queryBuilder->getRootAliases()[0];
//            $queryBuilder->andWhere(sprintf('%s.archived=false', $rootAlias));
        } else if (Application::class === $resourceClass) {

            $rootAlias = $queryBuilder->getRootAliases()[0];
            $queryBuilder->andWhere(sprintf('%s.deleted=false', $rootAlias));
        } else if (Notification::class === $resourceClass) {

            $rootAlias = $queryBuilder->getRootAliases()[0];
            $queryBuilder->andWhere(sprintf("%s.channel='internal'", $rootAlias));
            $queryBuilder->andWhere(sprintf("%s.deleted=false", $rootAlias));
        }

    }

    private function orderBy(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        if (JobPosition::class === $resourceClass) {
            $rootAlias = $queryBuilder->getRootAliases()[0];
            $queryBuilder->orderBy(sprintf('%s.active=1', $rootAlias), 'DESC');
            $queryBuilder->orderBy(sprintf('%s.startDate', $rootAlias), 'DESC');
        } else if (Education::class === $resourceClass) {
            $rootAlias = $queryBuilder->getRootAliases()[0];
            $queryBuilder->orderBy(sprintf('%s.startDate', $rootAlias), 'DESC');
        }
    }

}