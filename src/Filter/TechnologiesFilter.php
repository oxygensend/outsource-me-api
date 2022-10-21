<?php

namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\Query\Expr\Orx;
use Doctrine\ORM\QueryBuilder;

class TechnologiesFilter extends AbstractFilter
{
    public const PROPERTY = 'technologies';

    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = []): void
    {
        if ($property !== self::PROPERTY)
            return;

        $alias = $queryBuilder->getRootAliases()[0];

        $valueArray = explode(',', $value);

        $queryBuilder
            ->leftJoin(sprintf('%s.technologies', $alias), 't');

        $orx = new Orx();

        foreach ($valueArray as $parameter) {
            $valueParameter = $queryNameGenerator->generateParameterName(self::PROPERTY);
            $orx->add(sprintf('t.id = :%s', $valueParameter));
            $queryBuilder->setParameter($valueParameter, $parameter);
        }

        $queryBuilder->andWhere($orx);


    }

    public function getDescription(string $resourceClass): array
    {
        return [
            self::PROPERTY => [
                'property' => null,
                'type' => 'string',
                'required' => false,
                'openapi' => [
                    'description' => 'Find job offers with provided technologies',
                    'name' => 'technologies|array',
                    'type' => 'string[]|array'
                ]
            ]
        ];
    }


}