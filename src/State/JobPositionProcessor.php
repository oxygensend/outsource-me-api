<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Company;
use App\Entity\JobPosition;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class JobPositionProcessor implements ProcessorInterface
{
    public function __construct(private readonly ProcessorInterface     $decoratedProcessor,
                                private readonly EntityManagerInterface $em,
                                private readonly Security               $security)
    {
    }


    /**
     * @param JobPosition $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $company = $data->getCompany();

        if ($company->getId() === null) {
            $existingCompany = $this->em->getRepository(Company::class)->findOneBy(['name' => $company->getName()]);

            if ($existingCompany) {
                $data->setCompany($existingCompany);
            } else {
                $this->em->persist($company);
            }
        }

        $data->setIndividual($this->security->getUser());

        if (!$data->getValidTo()) {
            $data->setActive(true);
            $data->getIndividual()->setActiveJobPosition($data->getName());
        }

        $this->decoratedProcessor->process($data, $operation, $uriVariables, $context);
    }
}
