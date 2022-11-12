<?php

namespace App\Extensions\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\AboutUs;
use App\Entity\Application;
use App\Entity\Book;
use App\Entity\Category;
use App\Entity\JobOffer;
use Doctrine\ORM\QueryBuilder;

class EnabledExtension implements QueryCollectionExtensionInterface
{
    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = []): void
    {
        $this->andWhere($queryBuilder, $resourceClass);
    }

    private function andWhere(QueryBuilder $queryBuilder, string $resourceClass)
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
        }

    }

}