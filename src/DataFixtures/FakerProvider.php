<?php

namespace App\DataFixtures;

use App\Entity\JobOffer;
use App\Entity\SalaryRange;
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
        return $this->generator->text(random_int(5, 200));
    }

    public function chooseExperience(): string
    {
        return array_rand(JobOffer::EXPERIENCE_CHOICES);
    }

    public function chooseType(): string
    {
        return SalaryRange::TYPE_CHOICES[array_rand(SalaryRange::TYPE_CHOICES)];
    }

    public function chooseCurrency(): string
    {
        return SalaryRange::CURRENCIES_CHOICES[array_rand(SalaryRange::CURRENCIES_CHOICES)];
    }
}