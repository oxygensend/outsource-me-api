<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\CompanyRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
#[ApiResource(
    operations: [
        new GetCollection(
            paginationEnabled: false,
            normalizationContext: ['groups' => 'company:list']
        ),
    ],
)]
#[ORM\Entity(repositoryClass: CompanyRepository::class)]
class Company extends AbstractEntity
{
    #[Serializer\Groups(['user:profile', 'job_position:read', 'job_position:write', 'job_position:edit', 'company:list'])]
    #[ORM\Column(length: 255)]
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
