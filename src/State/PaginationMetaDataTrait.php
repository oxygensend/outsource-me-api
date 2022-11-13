<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;

trait PaginationMetaDataTrait
{

    protected function getPaginationMetaData(Operation $operation, array $context): array
    {
        $limit = $this->pagination->getLimit($operation);
        $page = $this->pagination->getPage($context);
        return [
            'limit' => $limit,
            'page' => $page,
            'offset' => ($page - 1) * $limit,
        ];

    }
}