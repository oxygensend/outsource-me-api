<?php

namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AddressSearchFilter extends AbstractFilter
{
    public const PROPERTY = 'search';

    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = []): void
    {
        if ($property !== self::PROPERTY)
            return;

        if (!preg_match("/^\d{2}-\d{3}$/", $value)) {
            throw new BadRequestHttpException('Invalid "search" format');
        }

        $alias = $queryBuilder->getRootAliases()[0];
        // a param name that is guaranteed unique in this query
        $valueParameter = $queryNameGenerator->generateParameterName('search');
        $queryBuilder
            ->andWhere(sprintf('%s.postCodes LIKE :%s', $alias, $valueParameter))
            ->setParameter($valueParameter, '%' . $value . '%');
    }


    public function getDescription(string $resourceClass): array
    {

        return [
            self::PROPERTY => [
                'property' => null,
                'type' => 'string',
                'required' => false,
                'openapi' => [
                    'description' => 'Find address accorded to  postal code',
                ]
            ]
        ];
    }

}