<?php

namespace App\DataFixtures;

use App\Entity\JobOffer;
use App\Entity\User;
use App\Repository\AddressRepository;
use App\Repository\TechnologyRepository;
use Fidry\AliceDataFixtures\ProcessorInterface;

final class FakerProcessor implements ProcessorInterface
{
    public function __construct(readonly private AddressRepository    $addressRepository,
                                readonly private TechnologyRepository $technologyRepository)
    {
    }

    public function preProcess(string $id, object $object): void
    {
        if ($object instanceof User || $object instanceof JobOffer) {
            /** @var  User|JobOffer $object */
            $object->setAddress($this->addressRepository->find(random_int(0, 3352)));
            $technologies = $this->technologyRepository->findAll();
            shuffle($technologies);
            for ($i = 0; $i < random_int(5, 20); $i++) {
                $object->addTechnology($technologies[$i]);
            }
        }

    }

    public function postProcess(string $id, object $object): void
    {
    }

}