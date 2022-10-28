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
            paginationEnabled: false,
            normalizationContext: ["groups" => "work_type:get"],
        )
    ]
)]
#[ORM\Entity(repositoryClass: WorkTypeRepository::class)]
class WorkType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['work_type:get'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['work_type:get', 'jobOffer:one'])]
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
