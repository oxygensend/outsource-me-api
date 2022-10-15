<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Company;
use App\Entity\JobPosition;
use App\Repository\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class JobPositionProcessor implements ProcessorInterface
{
    public function __construct(private readonly ProcessorInterface     $decoratedProcessor,
                                private readonly EntityManagerInterface $em)
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

        $this->decoratedProcessor->process($data, $operation, $uriVariables, $context);
    }
}
