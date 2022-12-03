<?php

namespace App\Controller\Api;

use App\Controller\ApiAbstractController;
use App\Entity\JobOffer;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class GetJobOfferAction extends ApiAbstractController
{


    public function __invoke(JobOffer $jobOffer): JobOffer
    {
        $jobOffer->increaseRedirect();
        $this->em->flush();

        return $jobOffer;
    }
}