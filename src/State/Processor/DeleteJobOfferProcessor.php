<?php

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\JobOffer;

class DeleteJobOfferProcessor implements ProcessorInterface
{
    public function __construct(readonly private ProcessorInterface $decoratedProcessor)
    {
    }

    /**
     * @param JobOffer $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
       $data->setArchived(true);

        $this->decoratedProcessor->process($data, $operation, $uriVariables, $context);

    }
}
