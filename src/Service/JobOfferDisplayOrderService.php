<?php

namespace App\Service;

use App\Entity\JobOffer;
use App\Entity\User;

class JobOfferDisplayOrderService
{

    public const TECHNOLOGY_WAGE = 2;

    public function calculateForYouDisplayOrder(array $jobOffers, User $user): array
    {
        /** @var JobOffer $jobOffer */
        foreach ($jobOffers as $jobOffer) {

            $randomRate = 0;
            if ($jobOffer->getAddress() === $user->getAddress() || $jobOffer->getAddress() === null) {
                $randomRate = random_int(1000, 10000);
            } else {
                $randomRate = random_int(100, 1000);
            }

            $userTechnologies = $user->getTechnologies()->toArray();
            $jobOfferTechnologies = $jobOffer->getTechnologies()->toArray();
            $technologiesIntersectionCount = count(array_intersect($userTechnologies, $jobOfferTechnologies));

            if ($technologiesIntersectionCount > 0) {
                $randomRate *= self::TECHNOLOGY_WAGE * $technologiesIntersectionCount / count($jobOfferTechnologies);
            }

            $jobOffer->setForYouOrder($randomRate);

        }

        usort($jobOffers, fn(JobOffer $el, JobOffer $el2) => $el2->getForYouOrder() <=> $el->getForYouOrder());


        return $jobOffers;
    }

}