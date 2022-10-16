<?php

namespace App\DataFixtures;

use Faker\Provider\Address;
use Faker\Provider\Base;

class FakerProvider extends Base
{


    public function generateCity(string $city, string $voivodeship) : string
    {
        return $city . ' Woj.' . $voivodeship;
    }

}