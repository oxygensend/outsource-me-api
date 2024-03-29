<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use App\Repository\TechnologyRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/technologies',
            cacheHeaders: [
                'max_age' => 84600,
                'shared_max_age' => 84600,
            ],
            paginationEnabled: false,
            normalizationContext: ["groups" => "technologies:get"],
        ),
        new Delete(
            uriTemplate: '/users/{userId}/technologies/{id}',
            uriVariables: [
                'userId' => new Link(fromProperty: 'technologies', fromClass: User::class),
                'id' => new Link(fromClass: Technology::class),
            ],
            security: "is_granted('ROLE_USER') and is_granted('DELETE_TECHNOLOGY', _api_normalization_context['uri_variables'])"
        )
    ]
)]
#[ApiFilter(SearchFilter::class, properties: ['name' => 'start'])]
#[ApiFilter(OrderFilter::class, properties: ['featured'])]
#[ORM\Entity(repositoryClass: TechnologyRepository::class)]
class Technology extends AbstractEntity
{

    #[Serializer\Groups(['technologies:get', 'user:profile-developer', 'jobOffer:one'])]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?bool $featured = false;

    public function __toString(): string
    {
        return $this->name;
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

    public function isFeatured(): ?bool
    {
        return $this->featured;
    }

    public function setFeatured(bool $featured): self
    {
        $this->featured = $featured;

        return $this;
    }


}
