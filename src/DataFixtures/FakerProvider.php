<?php

namespace App\DataFixtures;

use Faker\Provider\Base;

class FakerProvider extends Base
{

    public function generateCity(string $city, string $voivodeship): string
    {
        return $city . ' Woj.' . $voivodeship;
    }

    public function generateSalaryRange(): string
    {
        $baseRange = random_int(2000, 15000);
        return $baseRange . ' - ' . $baseRange + 3000;
    }

    public function generateUrl(): string
    {
       return $this->generator->url();
    }

    public function generateRandomLengthDescription(): string
    {
        return $this->generator->text(random_int(5,200));
    }

}