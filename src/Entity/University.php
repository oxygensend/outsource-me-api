<?php

namespace App\Entity;

use App\Repository\UniversityRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ORM\Entity(repositoryClass: UniversityRepository::class)]
class University extends AbstractEntity
{

    #[Serializer\Groups(["user:profile"])]
    #[ORM\Column(length: 255)]
    private ?string $country = null;

    #[Serializer\Groups(["user:profile"])]
    #[ORM\Column(length: 255)]
    private ?string $name = null;


    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
