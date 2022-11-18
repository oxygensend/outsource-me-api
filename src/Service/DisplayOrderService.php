<?php

namespace App\Service;

use App\Entity\JobOffer;
use App\Entity\User;

class DisplayOrderService
{
    public const TECHNOLOGY_WAGE = 2;
    public const EXPERIENCE_WAGES = [
        'Stażysta' => [
            'Stażysta' => 2,
            'Junior' => 1.5,
        ],
        'Junior' => [
            'Stażysta' => 1.4,
            'Junior' => 2,
            'Mid' => 1.5
        ],
        'Mid' => [
            'Junior' => 1.2,
            'Mid' => 2,
            'Senior' => 1.2

        ],
        'Senior' => [
            'Mid' => 1.2,
            'Senior' => 2,
        ]
    ];

    public const LOCALISATION_WAGE = 2;

    public function __construct(readonly private DistanceCalculatorService $distanceCalculator)
    {
    }

    public function calculateJobOfferForYouDisplayOrder(array $jobOffers, User $user): array
    {
        /** @var JobOffer $jobOffer */
        foreach ($jobOffers as $jobOffer) {

            // LOCALIZATION
            if ($jobOffer->getAddress() === $user->getAddress() || $jobOffer->getAddress() === null) {
                $randomRate = random_int(1000, 10000);
            } else {
                $randomRate = random_int(100, 1000);
                if ($user->getAddress()) {

                    $distance = $this->distanceCalculator
                        ->calculateDistanceBasedOnVincentyFormula($user->getAddress(), $jobOffer->getAddress());
                    $randomRate *= (self::LOCALISATION_WAGE - $distance / DistanceCalculatorService::THE_MOST_DISTANCE_BETWEEN_CITIES_IN_POLAND);

                }
            }

            // TECHNOLOGIES
            $userTechnologies = $user->getTechnologies()->toArray();
            $jobOfferTechnologies = $jobOffer->getTechnologies()->toArray();
            $technologiesIntersectionCount = count(array_intersect($userTechnologies, $jobOfferTechnologies));

            if ($technologiesIntersectionCount > 0) {
                $randomRate *= self::TECHNOLOGY_WAGE * $technologiesIntersectionCount / count($jobOfferTechnologies);
            }

            // EXPERIENCE
            $userExperience = $user->getExperience();
            $jobOfferExperience = $jobOffer->getExperience();
            $randomRate *= self::EXPERIENCE_WAGES[$userExperience][$jobOfferExperience] ?? 1;


            // OPINIONS
            $opinionsCount = $jobOffer->getUser()->getOpinions()->count();
            if ($opinionsCount > 0) {
                $randomRate *= (1 + $jobOffer->getUser()->getOpinionsRate() * $opinionsCount / 100);
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

            // Localization
            if (in_array($developer->getAddress(), $jobOffersLocalizations) || in_array(null, $jobOffersLocalizations)) {
                $randomRate = random_int(1000, 10000);
            } else {
                $randomRate = random_int(100, 1000);
            }

            // Technologies
            $developerTechnologies = $developer->getTechnologies()->toArray();
            $technologiesIntersectionCount = count(array_intersect($jobOffersTechnologies, $developerTechnologies));

            if ($technologiesIntersectionCount > 0) {
                $randomRate *= self::TECHNOLOGY_WAGE * $technologiesIntersectionCount / count($jobOffersTechnologies);
            }


            // Opinions
            $opinionsCount = $developer->getOpinions()->count();
            if ($opinionsCount > 0) {
                $randomRate *= (1 + $developer->getOpinionsRate() * $opinionsCount / 100);
            }
            $developer->setForYouOrder($randomRate);

        }

        usort($developers, fn(User $el, User $el2) => $el2->getForYouOrder() <=> $el->getForYouOrder());


        return $developers;
    }

}