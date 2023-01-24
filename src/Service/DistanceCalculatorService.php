<?php

namespace App\Service;

use App\Entity\Address;

class DistanceCalculatorService
{
    // EARTH RADIUS IN KM
    public const EARTH_RADIUS = 6371;
    // IN KM
    public const THE_LONGEST_DISTANCE_BETWEEN_CITIES_IN_POLAND = 1089;

    public function calculateDistanceBasedOnVincentyFormula(Address $address1, Address $address2): float
    {
        // Exit if coordinate is missing
        if (!$address1->getLon() || !$address1->getLat() || !$address2->getLat() || !$address2->getLon()){
            return 0;
        }

        $lonFrom = deg2rad($address1->getLon());
        $latFrom = deg2rad($address1->getLat());
        $lonTo = deg2rad($address2->getLon());
        $latTo = deg2rad($address2->getLat());

        $lonDelta = $lonFrom - $lonTo;

        $a = pow(cos($latTo) * sin($lonDelta), 2) +
            pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
        $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

        $angle = atan2(sqrt($a), $b);

        return $angle * self::EARTH_RADIUS;
    }

}