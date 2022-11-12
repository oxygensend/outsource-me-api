<?php

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Application;
use App\Entity\JobOffer;

class DeleteApplicationProcessor implements ProcessorInterface
{
    public function __construct(readonly private ProcessorInterface $decoratedProcessor)
    {
    }

    /**
     * @param Application $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $data->setDeleted(true);

        $this->decoratedProcessor->process($data, $operation, $uriVariables, $context);

    }
}
