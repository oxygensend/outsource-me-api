<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\TechnologyRepository;
use App\Repository\UniversityRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ApiResource(
    operations:[ new GetCollection(
        uriTemplate: '/technologies',
        paginationEnabled: false,
        normalizationContext: ["groups" => "technologies:get"],
        security: "is_granted('ROLE_USER')",
    )]
)]
#[ApiFilter(SearchFilter::class, properties: ['name' => 'start'])]
#[ORM\Entity(repositoryClass: TechnologyRepository::class)]
class Technology
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Serializer\Groups(['technologies:get', 'user:profile-developer'])]
    #[ORM\Column(length: 255)]
    private ?string $name = null;


    public function getId(): ?int
    {
        return $this->id;
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
