<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\JobOffer;
use Symfony\Component\Security\Core\Security;

class JobOfferProcessor implements ProcessorInterface
{
    public function __construct(readonly private ProcessorInterface $decoratedProcessor,
                                readonly private Security $security)
    {
    }

    /**
     * @param JobOffer $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {

        $data->setUser($this->security->getUser());


        $this->decoratedProcessor->process($data, $operation, $uriVariables, $context);
    }
}
