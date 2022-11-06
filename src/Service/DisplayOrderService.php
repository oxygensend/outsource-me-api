<?php

namespace App\Service;

use App\Entity\JobOffer;
use App\Entity\User;

class DisplayOrderService
{

    public const TECHNOLOGY_WAGE = 2;

    public function calculateJobOfferForYouDisplayOrder(array $jobOffers, User $user): array
    {
        /** @var JobOffer $jobOffer */
        foreach ($jobOffers as $jobOffer) {

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

    public function calculateDevelopersForYouDisplayOrder(array $developers, User $principle): array
    {
        /** Fetch technologies from all of users offers */
        $jobOffersTechnologies = [];
        $jobOffersLocalizations = [];

        foreach ($principle->getJobOffers() as $jobOffer) {
            $jobOffersTechnologies = array_merge($jobOffersTechnologies, $jobOffer->getTechnologies()->toArray());
            $jobOffersLocalizations[] = $jobOffer->getAddress();
        }
        $jobOffersTechnologies = array_unique($jobOffersTechnologies);
        $jobOffersLocalizations = array_unique($jobOffersLocalizations);


        /** @var User $developer */
        foreach ($developers as $developer) {

            if (in_array($developer->getAddress(), $jobOffersLocalizations) || in_array(null, $jobOffersLocalizations)) {
                $randomRate = random_int(1000, 10000);
            } else {
                $randomRate = random_int(100, 1000);
            }

            $developerTechnologies = $developer->getTechnologies()->toArray();
            $technologiesIntersectionCount = count(array_intersect($jobOffersTechnologies, $developerTechnologies));

            if ($technologiesIntersectionCount > 0) {
                $randomRate *= self::TECHNOLOGY_WAGE * $technologiesIntersectionCount / count($jobOffersTechnologies);
            }

            $developer->setForYouOrder($randomRate);

        }

        usort($developers, fn(User $el, User $el2) => $el2->getForYouOrder() <=> $el->getForYouOrder());


        return $developers;
    }

}