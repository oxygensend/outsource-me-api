<?php

namespace App\Controller\Api;

use App\Entity\JobOffer;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class GetJobOfferAction extends AbstractController
{

    public function __construct(readonly private  EntityManagerInterface $em)
    {
    }

    public function __invoke(JobOffer $jobOffer): JobOffer
    {
        $jobOffer->addRedirect();
        $this->em->flush();

        return $jobOffer;
    }
}