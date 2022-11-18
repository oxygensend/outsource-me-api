<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\FormOfEmploymentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/form_of_employments',
            cacheHeaders: [
                'max_age' => 84600,
                'shared_max_age' => 84600,
            ],
            paginationEnabled: false,
            normalizationContext: ["groups" => "foe:get"],
        )
    ]
)]
#[ORM\Entity(repositoryClass: FormOfEmploymentRepository::class)]
class FormOfEmployment extends AbstractEntity
{
    #[Serializer\Groups(['user:profile', 'job_position:read', 'foe:get', 'jobOffer:one'])]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

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
