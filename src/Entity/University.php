<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\UniversityRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ApiResource(
    operations:[ new GetCollection(
        uriTemplate: '/universities',
        paginationEnabled: false,
        normalizationContext: ["groups" => "universities:get"],
        security: "is_granted('ROLE_USER')",
    )]
)]
#[ApiFilter(SearchFilter::class, properties: ['name' => 'start'])]
#[ORM\Entity(repositoryClass: UniversityRepository::class)]
class University extends AbstractEntity
{

    #[Serializer\Groups(["user:profile"])]
    #[ORM\Column(length: 255)]
    private ?string $country = 'Poland';

    #[Serializer\Groups(["user:profile", "universities:get"])]
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
