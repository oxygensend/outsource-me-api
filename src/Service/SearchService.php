<?php

namespace App\Service;

use Elastica\Index;
use FOS\ElasticaBundle\Index\IndexManager;

/** Class responsible for operating with elasticsearch */
class SearchService
{
    private ?Index $index = null;
    private string|array|null $sourceFields = null;
    private array $order = [];

    public function __construct(readonly private IndexManager $indexManager)
    {
    }


    /**
     * @throws \Exception
     */
    public function search(string $searchQuery, array|null $pagination = null)
    {
        if (!$this->index) {
            throw  new \Exception('Index not found');
        }

        $query = [
            '_source' => $this->getSourceFields(),
            'query' => [
                'multi_match' => [
                    'query' => $searchQuery
                ]
            ],
            'sort' => $this->order
        ];

        if ($pagination) {
            $query['from'] = $pagination['offset'];
            $query['size'] = $pagination['limit'];

        }

        $response = $this->index->request('_search', 'GET', $query)->getData();
        return array_column($response['hits']['hits'], '_source');
    }

    /** [field => asc, ...] */
    public function setOrder(array $order): void
    {
        $elasticOrderQuery = [];
        foreach ($order as $item => $value) {
            if (!in_array(strtolower($value), ['asc', 'desc']))
                continue;
            $elasticOrderQuery[$item] = [
                'order' => strtolower($value)
            ];
        }

        $this->order = $elasticOrderQuery;

    }

    /** [field1, field2 ...] */
    public function setSourceFields(array $fields): void
    {
        $this->sourceFields = $fields;
    }

    private function getSourceFields(): array|string
    {
        return is_array($this->sourceFields) ? $this->sourceFields : '*';
    }

    public function setIndex(string $indexName): void
    {
        $this->index = $this->indexManager->getIndex($indexName);
    }

    public function getIndex(): ?Index
    {
        return $this->index;
    }
}