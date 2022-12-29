<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\WorkTypeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new GetCollection(
            cacheHeaders: [
                'max_age' => 84600,
                'shared_max_age' => 84600,
            ],
            paginationEnabled: false,
            normalizationContext: ["groups" => "work_type:get"],
        )
    ]
)]
#[ORM\Entity(repositoryClass: WorkTypeRepository::class)]
class WorkType extends AbstractEntity
{
    #[ORM\Column(length: 255)]
    #[Groups(['work_type:get', 'jobOffer:one'])]
    private ?string $name = null;


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
}
