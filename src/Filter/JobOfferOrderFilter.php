<?php

namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;

class JobOfferOrderFilter extends AbstractFilter
{
    public const PROPERTY = 'order';
    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = []): void
    {
        if(self::PROPERTY !== $property)
            return;

        $alias = $queryBuilder->getRootAliases()[0];

        switch ($value) {

            case "popular":
                $queryBuilder->orderBy(sprintf('%s.popularityOrder',$alias ), 'DESC');
                break;
            case "newest":
                $queryBuilder->orderBy(sprintf('%s.createdAt', $alias), 'DESC');
                break;
            case "normal":
                $queryBuilder->orderBy(sprintf('%s.displayOrder',$alias ), 'DESC');
                break;


        }
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            self::PROPERTY => [
                'property' => null,
                'type' => 'string',
                'required' => false,
                'openapi' => [
                    'description' => 'Change order of displayed job offers',
                    'name' => 'sort|string',
                    'type' => 'popular|newest|normal'
                ]
            ]
        ];    }


}