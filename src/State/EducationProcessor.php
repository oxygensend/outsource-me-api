<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Education;
use Symfony\Component\Security\Core\Security;

class EducationProcessor implements ProcessorInterface
{
    public function __construct(readonly private Security           $security,
                                readonly private ProcessorInterface $decoratedProcessor)
    {
    }

    /**
     * @param Education $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $data->setIndividual($this->security->getUser());

        $this->decoratedProcessor->process($data, $operation, $uriVariables, $context);
    }
}
