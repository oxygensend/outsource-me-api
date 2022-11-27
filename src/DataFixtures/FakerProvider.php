<?php

namespace App\DataFixtures;

use App\Entity\JobOffer;
use App\Entity\SalaryRange;
use App\Entity\WorkType;
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
        return $this->generator->text(random_int(200, 3000));
    }

    public function chooseExperience(): string
    {
        return JobOffer::EXPERIENCE_CHOICES[array_rand(JobOffer::EXPERIENCE_CHOICES)];
    }

    public function chooseType(): string
    {
        return SalaryRange::TYPE_CHOICES[array_rand(SalaryRange::TYPE_CHOICES)];
    }

    public function chooseCurrency(): string
    {
        return SalaryRange::CURRENCIES_CHOICES[array_rand(SalaryRange::CURRENCIES_CHOICES)];
    }

    public function chooseWorkType(): string
    {
        return '';
//        return WorkType::Ch[array_rand(SalaryRange::CURRENCIES_CHOICES)];
    }

    public function dateCloseToCurrent(): \DateTimeInterface|null
    {
        $current = new \DateTime();
        $rand = random_int(0, 6);
        if ($rand > 3) {
            return $current->modify(sprintf('+%d days', random_int(1, 40)));
        } elseif (!$rand) {
            return null;
        } else {
            return $current->modify(sprintf('-%d days', random_int(1, 40)));
        }
    }

    public function dateBiggerThan($dateFrom): \DateTimeInterface
    {
        return $dateFrom->modify(sprintf('+%d days', random_int(14, 365)));

    }

    public function generateName(): string
    {
       return $this->generator->firstName();
    }

    public function generateSurname(): string
    {
        return $this->generator->lastName();
    }

}